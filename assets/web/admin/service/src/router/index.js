/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import Router from 'vue-router';
import fetchScenario from './guards';

// Pages
import InstalledModulesPage from '../components/page/InstalledModulesPage';
import UpgradePage from '../components/page/UpgradePage';
import UpgradeDetailsPage from '../components/page/UpgradeDetailsPage';
import SuccessRebuildPage from '../components/page/SuccessRebuildPage';
import PageAccessDenied from '../components/page/PageAccessDenied';
import PageTechInfo from '../components/page/PageTechInfo';

// Headers
import ModulesHeader from '../components/header/ModulesHeader';
import PageHeader from '../components/header/PageHeader';
import UpgradeDetailsHeader from '../components/header/UpgradeDetailsHeader';

import appConfig from '../../config/app-config';

function maybeSwitchLeftMenu() {
  const menuItem = document.querySelector('.menu-item.extensions');
  if (menuItem) {
    const shouldLeftMenuItemBeCollapsed = (window.location.hash === '#/version');
    menuItem.classList.toggle('active', !shouldLeftMenuItemBeCollapsed);
    menuItem.classList.toggle('expanded', !shouldLeftMenuItemBeCollapsed);
    menuItem.classList.toggle('collapsed', shouldLeftMenuItemBeCollapsed);
  }
}

export default function (store) {
  let routes = [
    {
      path: '/',
      redirect: '/installed-addons',
    },
    {
      path: '/installed-addons',
      components: {
        default: InstalledModulesPage,
        header: ModulesHeader,
      },
      props: {
        default: route => ({ filters: route.query }),
        header: { title: 'navigation.my_addons', controlsType: 'ModulesControls' },
      },
      beforeEnter: fetchScenario(store, 'common'),
    },
    {
      path: '/custom-modules',
      components: {
        default: InstalledModulesPage,
        header: PageHeader,
      },
      props: {
        header: { title: 'upgrade-details-page.custom-modules-warning.custom-modules' },
      },
      beforeEnter: fetchScenario(store, 'common'),
    },
    {
      path: '/success-rebuild',
      components: {
        default: SuccessRebuildPage,
        header: PageHeader,
      },
      beforeEnter: fetchScenario(store, 'common'),
    },
  ];

  if (window.xliteConfig.display_upgrade_notifications) {
    routes = routes.concat([
      {
        path: '/upgrade',
        components: {
          default: UpgradePage,
          header: PageHeader,
        },
        props: {
          header: { title: 'upgrade-page-heading' },
        },
      },
      {
        path: '/upgrade/:type',
        components: {
          default: UpgradeDetailsPage,
          header: UpgradeDetailsHeader,
        },
        props: {
          header: { title: 'upgrade-page-heading' },
        },
        children: [
          {
            path: '/upgrade/:type/renewals/:renewalsResult',
          },
        ],
      },
    ]);
  } else {
    routes = routes.concat([
      {
        path: '/upgrade',
        components: {
          default: PageAccessDenied,
        },
      },
      {
        path: '/upgrade/:type',
        components: {
          default: PageAccessDenied,
        },
      },
    ]);
  }

  if (appConfig.isRootAdmin) {
    routes.push({
      path: '/version',
      components: {
        default: PageTechInfo,
        header: PageHeader,
      },
      props: {
        header: { title: 'tech-info.title' },
      },
      beforeRouteEnter(to, from, next) {
        // eslint-disable-next-line no-console
        console.log('Before Route Enter.');
        // eslint-disable-next-line no-console
        console.log({ from, to, next });
        next();
      },
    });
  }

  window.addEventListener('hashchange', maybeSwitchLeftMenu);
  maybeSwitchLeftMenu();

  return new Router({ routes });
}
