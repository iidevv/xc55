/* eslint-disable no-var */
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// import Vue from 'vue';
import Vuex from 'vuex';
import Router from 'vue-router';
import VModal from 'vue-js-modal';
import vPagination from 'vue-plain-pagination';
import VTooltip from 'v-tooltip';
import VueCookies from 'vue-cookies';
import VueScroll from 'vue-scroll';
import Nl2br from 'vue-nl2br';
import App from './App';
import Icon from './components/block/Icon';
import Minicart from './Minicart';
import ModuleModal from './ModuleModal';
import Search from './Search';
import createRouter from './router';
import createStore from './store';
import i18n from './i18n/i18n';
import ExternalLink from './components/block/ExternalLink';

/* eslint-disable no-undef */
Vue.config.productionTip = false;
Vue.use(Vuex);
Vue.use(Router);
Vue.use(VModal);
Vue.use(VTooltip);
Vue.use(VueCookies);
Vue.use(VueScroll);

Vue.component('v-pagination', vPagination);
Vue.component('nl2br', Nl2br);
Vue.component('icon', Icon);
Vue.component('a-external', ExternalLink);

Vue.$cookies.config('1d');
/* eslint-disable no-undef */

const store = createStore();
const router = createRouter(store);

/* eslint-disable no-new,no-unused-vars,no-vars,vars-on-top */
const myApps = new Vue({
  el: '#app',
  router,
  store,
  i18n,
  components: { App },
  template: '<App/>',
});

const minicart = new Vue({
  el: '#minicart-controller',
  store,
  i18n,
  components: { Minicart },
  template: '<Minicart/>',
});

const moduleModal = new Vue({
  el: '#module-modal',
  router,
  store,
  i18n,
  components: { ModuleModal },
  template: '<ModuleModal/>',
});

const searchBar = new Vue({
  el: '#search-bar',
  store,
  router,
  i18n,
  components: { Search },
  template: '<Search/>',
});
