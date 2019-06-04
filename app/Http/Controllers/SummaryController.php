<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Order, GripTape};
use App\Models\ShipInfo;
use Illuminate\Support\Facades\Auth;
use Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;
use Itlead\Promocodes\Models\Promocode;
use Cookie;

class SummaryController extends Controller
{
    protected $feesTypes = [
        'engravery' => [
            'name' => 'Top Engravery Set Up',
            'price' => 80
        ],
        'topprint' => [
            'name' => 'Top Print Set Up',
            'price' => 120
        ],
        'bottomprint' => [
            'name' => 'Bottom Print Set Up',
            'price' => 120
        ],
        'carton' => [
            'name' => 'Box print Set Up',
            'price' => 120
        ],
        'cardboard' => [
            'name' => 'Cardboard Set Up',
            'price' => 500
        ],
        // Grip tapes
        'top_print' => [
            'name' => 'Grip Top Print',
            'price' => 30
        ],
        'die_cut' => [
            'name' => 'Grip tape die_cut',
            'price' => 80
        ],
        'carton_print' => [
            'name' => 'Griptape Carton Print',
            'price' => 95
        ],
        'backpaper_print' => [
            'name' => 'Griptape Backpaper Print',
            'price' => 45
        ],
    ];
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ordersQuery = Order::auth();
        $gripQuery = GripTape::auth();

        // Order weight
        $gripWeight = (clone $gripQuery)->get()->reduce(function($carry, $item) {
            return $carry + ($item->quantity * GripTape::sizePrice($item->size)['weight']); 
        }, 0);

        // total weight
        $weight = ($ordersQuery->sum('quantity') * Order::SKATEBOARD_WEIGHT) + $gripWeight;

        // Fetching all desing by orders
        $orders = (clone $ordersQuery)
            ->select('quantity', 'bottomprint', 'topprint', 'engravery', 'cardboard', 'carton')
            ->get()
            ->map(function($order) {
                return array_filter($order->attributesToArray());
            })
            ->toArray();


        // Fetching all desing by griptapes
        $gripTapes = (clone $gripQuery)
            ->get()
            ->map(function($grip) {
                return array_filter($grip->attributesToArray());
            })
            ->toArray();

        $fees = [];
        $sum_fees = 0;

        foreach ($orders as $index => $order) {
            $index += 1;

            foreach ($order as $key => $value) {
                if (!array_key_exists($key,  $this->feesTypes)) continue;

                // If same design
                if (array_key_exists($key, $fees)) {
                    if (array_key_exists($value, $fees[$key])) {
                        $fees[$key][$value]['batches'] .= ",{$index}";
                        $fees[$key][$value]['quantity'] += $order['quantity'];

                        if ($key == 'cardboard') {
                            $fees[$key][$value]['price'] = Order::getPriceDesign($fees[$key][$value]['quantity']);
                        }
                        continue;
                    }
                } 
                $fees[$key][$value] = [
                    'image'    => $value,
                    'batches'  => (string) $index,
                    'type'     => $this->feesTypes[$key]['name'],
                    'quantity' => $order['quantity'],
                ];

                /*
                 * Cardboard price calculated 
                 * Formula: 500 + (quantity - 625) * 0.8
                 * If (quantity - 625) * 0.8 < 0 then 0
                 */ 
                if ($key == 'cardboard') {
                    $fees[$key][$value]['price'] = Order::getPriceDesign($order['quantity']);
                } else {
                    $fees[$key][$value]['price'] = $this->feesTypes[$key]['price'];
                }

            }
        }

        foreach ($gripTapes as $index => $grip) {
            $index += 1;

            foreach ($grip as $key => $value) {

                if (!array_key_exists($key,  $this->feesTypes)) continue;

                // If same design
                if (array_key_exists($key, $fees)) {
                    if (array_key_exists($value, $fees[$key])) {
                        $fees[$key][$value]['batches'] .= ",{$index}";
                        $fees[$key][$value]['quantity'] += $grip['quantity'];
                        continue;
                    }
                } 
                $fees[$key][$value] = [
                    'image'    => $value,
                    'batches'  => (string) $index,
                    'type'     => $this->feesTypes[$key]['name'],
                    'quantity' => $grip['quantity'],
                    'color'    => 1
                ];

                if (array_key_exists($key . '_color', $grip)) {
                    switch ($grip[$key . '_color']) {
                        case '1 color':
                            $fees[$key][$value]['color'] = 1;
                            break;
                        case '2 color':
                            $fees[$key][$value]['color'] = 2;
                            break;
                        case '3 color':
                            $fees[$key][$value]['color'] = 3;
                            break;
                        case 'CMYK':
                            $fees[$key][$value]['color'] = 4;
                            break;
                    }
                }

                $fees[$key][$value]['price'] = $this->feesTypes[$key]['price'] * $fees[$key][$value]['color'];
            }
        }

        // Set Global delivery
        if ($ordersQuery->count() || $gripQuery->count()) {
            $fees['global'] = [];
            array_push($fees['global'], [
                'image' => auth()->check() ? $weight . ' KG' : '$?.??', 
                'batches' => '', 
                'price' => get_global_delivery($weight), 
                'type' => 'Global delivery'
            ]);
        }

        // Calculate total price
        foreach ($fees as $key => $value) {
            array_walk($value, function($f, $k) use(&$sum_fees){
                $sum_fees += $f['price'];
            });
        }

        // calculate total 
        $totalOrders = $ordersQuery->sum('total') + GripTape::auth()->sum('total') + $sum_fees;

        $promocode = $ordersQuery->count() ? $ordersQuery->first()->promocode : false;

        if ($promocode) {
            
            $promocode = json_decode($promocode);

            switch ($promocode->type) {
                case Promocode::FIXED :
                    $totalOrders -= $promocode->reward;
                    break;
                
                case Promocode::PERCENT:
                    $totalOrders -= $totalOrders * $promocode->reward / 100;
                    break;
            }
        }

        Cookie::queue('orderTotal', $totalOrders);

        return view('summary', compact('fees', 'totalOrders'));
    }

    public function exportcsv()
    {
        $queryOrders = Order::auth();
        $gripQuery = GripTape::auth();

        dispatch($exporter = new \App\Jobs\GenerateInvoicesXLSX($queryOrders->get(), $gripQuery->get()));

        $queryOrders->update(['invoice_number' => $exporter->getInvoiceNumber()]);

        return response()->download($exporter->getPathInvoice());
    }

    public function exportcsvbyid($id)
    {
        $save_data['usenow'] = 0;
        //$save_data['saved_date'] =new \DateTime();

        $created_by = (string) (auth()->check() ? auth()->id() : csrf_token());

        Order::where('created_by','=',$created_by)->where('usenow', '=', 1)->update($save_data);
        GripTape::where('created_by','=',$created_by)->where('usenow', '=', 1)->update($save_data);

        $data = Order::where('created_by','=',$created_by)->where('saved_date', '=', $id)->get();
        $grips = GripTape::where('created_by','=',$created_by)->where('saved_date', '=', $id)->get();

        for($i = 0; $i < count($data); $i ++){
            unset($data[$i]['id']);
            unset($data[$i]['saved_date']);
            unset($data[$i]['usenow']);
            unset($data[$i]['submit']);
            $array = json_decode(json_encode($data[$i]), true);
            Order::insert($array);
        }

        for($i = 0; $i < count($grips); $i ++){
            unset($grips[$i]['id']);
            unset($grips[$i]['saved_date']);
            unset($grips[$i]['usenow']);
            unset($grips[$i]['submit']);
            $array = json_decode(json_encode($grips[$i]), true);
            GripTape::insert($array);
        }

        $orders = Order::auth()->get();
        $grips = GripTape::auth()->get();

        $exporter = new \App\Jobs\GenerateInvoicesXLSX($orders, $grips);

        $model = $orders->count() ? $orders->first() : $grips->first();

        $exporter->setInvoiceNumber($model->invoice_number);

        $exporter->setDate($model->created_at->timestamp);

        dispatch($exporter);
        
        return response()->download($exporter->getPathInvoice());
    }

    public function submitOrder()
    {
        $info = ShipInfo::auth()->select('invoice_name')->first(); 
        $queryOrders = Order::auth();
        $queryGripTapes = GripTape::auth();

        Mail::to(auth()->user())->send(new \App\Mail\OrderSubmit($info->toArray()));

        $now = now();

        $queryOrders->update([
            'submit' => 1,
            'saved_date' => $now
        ]);

        $queryGripTapes->update([
            'submit' => 1,
            'saved_date' => $now
        ]);

        session()->flash('success', 'Your order has been successfully sent!'); 

        return redirect()->route('summary');
    }

    public function saveOrder(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);

        if(Auth::user()){
            $created_by = (string) auth()->id();
        }
        else{
            $created_by = csrf_token();
        }

        $data = Order::where('created_by','=',$created_by)->where('usenow', '=', 1)->get();
        $grips = GripTape::where('created_by','=',$created_by)->where('usenow', '=', 1)->get();

        $save_data['usenow'] = 0;
        $save_data['saved_date'] = now();
        $save_data['saved_name'] = $request->get('name', $save_data['saved_date']);

        Order::where('created_by','=',$created_by)->where('usenow', '=', 1)->update($save_data);
        GripTape::where('created_by','=',$created_by)->where('usenow', '=', 1)->update($save_data);

        for($i = 0; $i < count($data); $i ++){
            unset($data[$i]['id']);
            unset($data[$i]['saved_date']);
            $array = json_decode(json_encode($data[$i]), true);

            Order::insert($array);
        }

        for($i = 0; $i < count($grips); $i ++){
            unset($grips[$i]['id']);
            unset($grips[$i]['saved_date']);
            $array = json_decode(json_encode($grips[$i]), true);
            GripTape::insert($array);
        }

        return redirect()->route('summary');   
    }
    public function load($id)
    {
        $save_data['usenow'] = 0;
        //$save_data['saved_date'] =new \DateTime();

        
        if(Auth::user()){
            $created_by = (string) auth()->id();
        }
        else{
            $created_by = csrf_token();
        }
        Order::where('created_by','=',$created_by)->where('usenow', '=', 1)->update($save_data);
        GripTape::where('created_by','=',$created_by)->where('usenow', '=', 1)->update($save_data);

        $data = Order::where('created_by','=',$created_by)->where('saved_date', '=', $id)->get();
        $grips = GripTape::where('created_by','=',$created_by)->where('saved_date', '=', $id)->get();

        for($i = 0; $i < count($data); $i ++){
            unset($data[$i]['id']);
            unset($data[$i]['saved_date']);
            unset($data[$i]['usenow']);
            unset($data[$i]['submit']);
            $array = json_decode(json_encode($data[$i]), true);
            Order::insert($array);
        }

        for($i = 0; $i < count($grips); $i ++){
            unset($grips[$i]['id']);
            unset($grips[$i]['saved_date']);
            unset($grips[$i]['usenow']);
            unset($grips[$i]['submit']);
            $array = json_decode(json_encode($grips[$i]), true);
            GripTape::insert($array);
        }

        return redirect()->route('summary');   
    } 
    public function view($id)
    {
        $save_data['usenow'] = 0;
        //$save_data['saved_date'] =new \DateTime();

        
        if(Auth::user()){
            $created_by = (string) auth()->id();
        }
        else{
            $created_by = csrf_token();
        }
        Order::where('created_by','=',$created_by)->where('usenow', '=', 1)->update($save_data);
        GripTape::where('created_by','=',$created_by)->where('usenow', '=', 1)->update($save_data);

        $data = Order::where('created_by','=',$created_by)->where('saved_date', '=', $id)->get();
        $grips = GripTape::where('created_by','=',$created_by)->where('saved_date', '=', $id)->get();

        for($i = 0; $i < count($data); $i ++){
            unset($data[$i]['id']);
            unset($data[$i]['saved_date']);
            unset($data[$i]['usenow']);
            unset($data[$i]['submit']);
            $array = json_decode(json_encode($data[$i]), true);
            Order::insert($array);
        }
        for($i = 0; $i < count($grips); $i ++){
            unset($grips[$i]['id']);
            unset($grips[$i]['saved_date']);
            unset($grips[$i]['usenow']);
            unset($grips[$i]['submit']);
            $array = json_decode(json_encode($grips[$i]), true);
            GripTape::insert($array);
        }

        return redirect()->route('summary')->with(['viewonly'=>1]);   
    }
    public function removeOrder($id)
    {
        if(Auth::user()){
            $created_by = (string) auth()->id();
        }
        else{
            $created_by = csrf_token();
        }
        Order::where('created_by','=',$created_by)->where('saved_date','=',$id)->delete();
        GripTape::where('created_by','=',$created_by)->where('saved_date','=',$id)->delete();

        return redirect()->route('profile');
    }

    public function applyPromocode(Request $request)
    {
        $this->validate($request, ['promocode' => 'required|min:2']);

        $checkCode = $request->get('promocode', '');

        $promocode = \Promocodes::check($checkCode);

        if (!$promocode) {
            return response()->json(["errors" => "The given data was invalid."], 400);
        }

        $queryOrders = Order::auth();
        $totalOrders = $queryOrders->sum('total');

        if (($promocode->is_supplement && $totalOrders >= 300) 
            || (!$promocode->is_supplement && $totalOrders >= 500)
        ) {
            $response = \Promocodes::apply($checkCode);

            list($code, $promocode) = $response;

            // set discount for all orders
            $queryOrders->update([
                'promocode' => json_encode(
                    array_merge($promocode->toArray(), ['code' => $code])
                )
            ]);

            return response()->json($code);
        }

        return response()->json(["errors" => "The given data was invalid."], 400);
    }
}
