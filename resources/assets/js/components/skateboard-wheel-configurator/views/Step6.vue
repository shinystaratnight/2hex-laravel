<template>
    <div class="m-wizard__form-step" id="m_wizard_form_step_6">
        <div class="row">
            <div class="col-xl-6">
                <div class="m-portlet m-portlet--bordered-semi m-portlet--widget-fit m-portlet--full-height m-portlet--skin-light  m-portlet--rounded-force">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">Pack as Square</h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="hover">

                                    <button
                                        class="btn m-btn m-btn--icon m-btn--icon-only m-btn--pill"
                                        :class="[placement === PLACEMENTS.SQUARE ? 'btn-success m-btn--custom' : 'btn-secondary btn-lg']"
                                        @click="placement = PLACEMENTS.SQUARE"
                                    >
                                        <i class="fa" :class="[placement === PLACEMENTS.SQUARE ? 'fa-check' : 'fa-times']"></i>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-widget17">
                            <div class="m-widget17__visual m-widget17__visual--chart m-portlet-fit--top m-portlet-fit--sides">
                                <div >
                                    <div 
                                        class="m-widget19__pic m-portlet-fit--top m-portlet-fit--sides" 
                                        style="min-height: 286px"
                                    >
                                        <img 
                                            src="/img/Wheels/skateboard-wheels-packaging/custom-skateboard-wheels-packed-as-square.jpg"
                                            alt="Pack as Square" 
                                            title="Pack as Square"
                                            class="step1-img2"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div style="text-align: justify; color: #9699a4;margin-top: 20px;">
                                <h3>Pack as Square</h3>
                                Skateboard wheels are commonly shrink wrapped in this square shape.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="m-portlet m-portlet--bordered-semi m-portlet--widget-fit m-portlet--full-height m-portlet--skin-light  m-portlet--rounded-force">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">Pack in Line</h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="hover">

                                    <button
                                        class="btn m-btn m-btn--icon m-btn--icon-only m-btn--pill"
                                        :class="[placement === PLACEMENTS.LINE ? 'btn-success m-btn--custom' : 'btn-secondary btn-lg']"
                                        @click="placement = PLACEMENTS.LINE"
                                    >
                                        <i class="fa" :class="[placement === PLACEMENTS.LINE ? 'fa-check' : 'fa-times']"></i>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-widget17">
                            <div class="m-widget17__visual m-widget17__visual--chart m-portlet-fit--top m-portlet-fit--sides">
                                <div >
                                    <div 
                                        class="m-widget19__pic m-portlet-fit--top m-portlet-fit--sides" 
                                        style="min-height: 286px"
                                    >
                                        <img 
                                            src="/img/Wheels/skateboard-wheels-packaging/custom-skateboard-wheels-packed-as-line.jpg"
                                            alt="Pack in Line" 
                                            title="Pack in Line"
                                            class="step1-img2"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div style="text-align: justify; color: #9699a4;margin-top: 20px;">
                                <h3>Pack in Line</h3>
                                The second most common way to pack skateboard wheels is as a line. This can help wheels to stand out in skateshops.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="m-portlet m-portlet--bordered-semi m-portlet--widget-fit m-portlet--full-height m-portlet--skin-light  m-portlet--rounded-force">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">Pack as Roll</h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="m-portlet__nav">
                                <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="hover">

                                    <button
                                        class="btn m-btn m-btn--icon m-btn--icon-only m-btn--pill"
                                        :class="[placement === PLACEMENTS.ROLL ? 'btn-success m-btn--custom' : 'btn-secondary btn-lg']"
                                        @click="placement = PLACEMENTS.ROLL"
                                    >
                                        <i class="fa" :class="[placement === PLACEMENTS.ROLL ? 'fa-check' : 'fa-times']"></i>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-widget17">
                            <div class="m-widget17__visual m-widget17__visual--chart m-portlet-fit--top m-portlet-fit--sides">
                                <div >
                                    <div 
                                        class="m-widget19__pic m-portlet-fit--top m-portlet-fit--sides" 
                                        style="min-height: 286px"
                                    >
                                        <img 
                                            src="/img/Wheels/skateboard-wheels-packaging/custom-skateboard-wheels-packed-as-roll.jpg"
                                            alt="Pack as Roll" 
                                            title="Pack as Roll"
                                            class="step1-img2"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div style="text-align: justify; color: #9699a4;margin-top: 20px;">
                                <h3>Pack as Roll</h3>
                                Some brands prefer to pack their wheels as rolls. These brands often add a custom cardboard to wrap around the wheels.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</template>

<script>
    import {PLACEMENTS} from '@/constants.js';

    export default {
        name: 'skateboard-wheel-step-6',
        data() {
            return {}
        },
        computed: {
            placement: {
                get() {
                    return this.$store.getters['SkateboardWheelConfigurator/getPlacement'];
                },
                set(newVal) {
                    this.$store.commit('SkateboardWheelConfigurator/changePlacement', newVal);
                }
            }
        },
        created() {
            this.PLACEMENTS = PLACEMENTS
        }
    }
</script>