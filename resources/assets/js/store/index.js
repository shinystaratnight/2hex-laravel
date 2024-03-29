import Vue from "vue";
import Vuex from "vuex";

import gripTapeConfigurator from './modules/griptape-configurator';
import SkateboardWheelConfigurator from './modules/skateboard-wheel-configurator';
import HeatTransferConfigurator from './modules/heat-transfer-configurator';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
    	currentStep: 0,
        isSaveOrderLater: false
    },
    mutations: {
    	changeStep(state, payload) {
    		state.currentStep = payload;
    	},
        changeIsLaterModal(state, payload) {
            state.isSaveOrderLater = payload;
        }
    },
    actions: {},
    getters: {
        getCurrentStep: state => state.currentStep,
    	getIsLater: state => state.isSaveOrderLater,
    },
    modules: {
    	gripTapeConfigurator,
        SkateboardWheelConfigurator,
        HeatTransferConfigurator
    }
});
