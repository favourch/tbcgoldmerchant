require('./bootstrap');

window.Vue = require('vue');

Vue.prototype.$baseUrl = 'https://tbcgoldmerchant.org';
// Vue.prototype.$baseUrl = 'http://localhost:8000';

import VeeValidate from 'vee-validate';
Vue.use(VeeValidate);

import { Notification, Popover, Button, Dialog, Radio, RadioGroup } from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(Popover);
Vue.use(Button);
Vue.use(Dialog);
Vue.use(Radio);
Vue.use(RadioGroup);
Vue.prototype.$notify = Notification;

Vue.use(require('vue-moment'));
import Vue2Filters from 'vue2-filters';
Vue.use(Vue2Filters);

import VueClipboard from 'vue-clipboard2';

Vue.use(VueClipboard);

// Member Components
Vue.component('member-auth-sign-up', require('./components/Member/Auth/MemberAuthSignUp.vue'));
Vue.component('member-auth-sign-up-with-sponsor', require('./components/Member/Auth/MemberAuthSignUpWithSponsor.vue'));
Vue.component('member-auth-sign-in', require('./components/Member/Auth/MemberAuthSignIn.vue'));

Vue.component('member-account', require('./components/Member/MemberAccount.vue'));
Vue.component('member-genology', require('./components/Member/MemberGenology.vue'));
Vue.component('member-genology2', require('./components/Member/MemberGenology2.vue'));
Vue.component('member-pass-recruit', require('./components/Member/MemberPassRecruit.vue'));
Vue.component('member-bonus-points-logs', require('./components/Member/MemberBonusPointsLogs.vue'));
Vue.component('member-merchants', require('./components/Member/MemberMerchants.vue'));
Vue.component('member-transactions', require('./components/Member/MemberTransactions.vue'));
Vue.component('member-cashout-points', require('./components/Member/MemberCashoutPoints.vue'));
Vue.component('member-cashout-points-matrix-2', require('./components/Member/MemberCashoutPointsMatrix2.vue'));
Vue.component('member-cashout-point-details', require('./components/Member/MemberCashoutPointDetails.vue'));
Vue.component('member-monthly-maintenance', require('./components/Member/MemberMonthlyMaintenance.vue'));
Vue.component('member-cashout-history', require('./components/Member/MemberCashoutHistory.vue'));
Vue.component('member-downline-add', require('./components/Member/MemberDownlineAdd.vue'));
Vue.component('member-cashout-referral', require('./components/Member/MemberCashoutReferral.vue'));
Vue.component('member-cashout-pairing', require('./components/Member/MemberCashoutPairing.vue'));
Vue.component('member-cashout-unilevel', require('./components/Member/MemberCashoutUnilevel.vue'));

Vue.component('member-membership-upgrade', require('./components/Member/MemberMembershipUpgrade.vue'));


// Admin Components
Vue.component('admin-auth-sign-up', require('./components/Admin/Auth/AdminAuthSignUp.vue'));
Vue.component('admin-auth-sign-in', require('./components/Admin/Auth/AdminAuthSignIn.vue'));

Vue.component('admin-members', require('./components/Admin/Member/AdminMembers.vue'));
Vue.component('admin-member-add', require('./components/Admin/Member/AdminMemberAdd.vue'));
Vue.component('admin-member-edit', require('./components/Admin/Member/AdminMemberEdit.vue'));

Vue.component('admin-merchants', require('./components/Admin/Merchant/AdminMerchants.vue'));
Vue.component('admin-merchant-add', require('./components/Admin/Merchant/AdminMerchantAdd.vue'));
Vue.component('admin-merchant-update', require('./components/Admin/Merchant/AdminMerchantUpdate.vue'));

Vue.component('admin-membership-transactions', require('./components/Admin/MembershipTransaction/AdminMembershipTransactions.vue'));
Vue.component('admin-membership-transaction-details', require('./components/Admin/MembershipTransaction/AdminMembershipTransactionDetails.vue'));

Vue.component('admin-cashout-transactions', require('./components/Admin/CashoutTransaction/AdminCashoutTransactions.vue'));
Vue.component('admin-cashout-transaction-details', require('./components/Admin/CashoutTransaction/AdminCashoutTransactionDetails.vue'));

Vue.component('admin-cashout-transactions-matrix-2', require('./components/Admin/CashoutTransaction2/AdminCashoutTransactions.vue'));
Vue.component('admin-cashout-transaction-matrix-2-details', require('./components/Admin/CashoutTransaction2/AdminCashoutTransactionDetails.vue'));


Vue.component('admin-maintenance-transactions', require('./components/Admin/MaintenanceTransaction/AdminMaintenanceTransactions.vue'));
Vue.component('admin-maintenance-transaction-details', require('./components/Admin/MaintenanceTransaction/AdminMaintenanceTransactionDetails.vue'));

Vue.component('admin-unilevel-transactions', require('./components/Admin/UnilevelTransaction/AdminUnilevelTransactions.vue'));
Vue.component('admin-unilevel-transaction-details', require('./components/Admin/UnilevelTransaction/AdminUnilevelTransactionDetails.vue'));

Vue.component('admin-account', require('./components/Admin/AdminAccount'));

Vue.component('admin-messages', require('./components/Admin/Message/AdminMessages.vue'));

Vue.component('admin-member-bonus-logs', require('./components/Admin/Member/AdminMemberBonusLogs.vue'));

Vue.component('includes-carousel', require('./components/Includes/IncludesCarousel.vue'));
Vue.component('includes-home-carousel', require('./components/Includes/IncludesHomeCarousel.vue'));
Vue.component('includes-countdown-timer', require('./components/Includes/IncludesCountdownTimer.vue'));
Vue.component('includes-btc-exchange-rates', require('./components/Includes/IncludesBtcExchangeRates.vue'));
Vue.component('includes-tbc-rates', require('./components/Includes/IncludesTbcRates.vue'));
Vue.component('includes-messages', require('./components/Includes/IncludesMessages.vue'));

Vue.component('contact-us', require('./components/Member/ContactUs.vue'));

const app = new Vue({
    el: '#app'
});
