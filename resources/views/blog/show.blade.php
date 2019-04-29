@extends('layouts.app')
@push('head.styles')
	<style>
		.r-side-flex {
		    display: -webkit-box;
		    display: -ms-flexbox;
		    display: flex;
		    -webkit-box-orient: vertical;
		    -webkit-box-direction: normal;
		        -ms-flex-direction: column;
		            flex-direction: column;
		    position: fixed;
		}
		.r-side-flex .btn {
		    margin: 5px;
		}
	</style>
@endpush
@section('content')
	<div class="m-grid__item m-grid__item--fluid m-wrapper">
		<div class="m-content">
			<div class="row">
				<div class="col-md-9">
					<div class="m-portlet">
						<div class="m-portlet__head">
							<div class="m-portlet__head-caption">
								<div class="m-portlet__head-title">
									<h3 class="m-portlet__head-text" id="imprint">
										The Skateboard Company Founder's Blog
									</h3>
								</div>
							</div>
						</div>
                                    
						<div class="m-wizard m-wizard--1 m-wizard--success" id="m_wizard">
							<div class="m-portlet__padding-x"></div>
							<div class="m-form m-form--label-align-left- m-form--state-">
								<div class="m-form__actions m-form__actions">
                                    <img 
                                    	src="{{ $post->image }}" 
                                    	style="width: 100%"
                                	>
									<br>
                                    <h2 class="m-portlet__head-text mt-5" id="imprint">
                                         {{$post->title}}
                                    </h2>
									<p>{!! $post->content !!}</p>                                          
                                </div>
                            </div>
                        </div>
					</div>
				</div>   
				@if(auth()->user()->isAdmin())
					<div class="col-md-3">
						<form action="{{ route('blog.destroy', $post->id) }}" method="POST" class="r-side-flex">
			                {{ csrf_field() }}
			                {{ method_field('DELETE') }}
							<a href="{{ route('blog.create') }}" class="btn btn-outline-success">New Post</a>
							<a href="{{ route('blog.edit', $post->id) }}" class="btn btn-outline-warning">Edit</a>
			                <button type="submit" class="btn btn-outline-danger">Remove</button>
			            </form>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
