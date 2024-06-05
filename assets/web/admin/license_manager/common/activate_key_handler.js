/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Activate license key handler
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('common/activateKeyHandler', ['common/coreLicense'], coreLicense => {
  return (successAction, failAction) => {
    return {
      license: null,
      isError: false,
      isCoreLicense: function () {
        return this.license.keyType === 2
      },
      getModuleId: function () {
        return this.license.moduleId
      },
      getModuleHash: function () {
        return this.license.module.moduleId
      },
      getModuleInfo: function () {
        return this.license.module
      },
      getMessage: function () {
        return this.license.message
      },
      splitModuleId: function () {
        return this.getModuleId().split('-')
      },
      getModuleName: function () {
        return this.splitModuleId()[1]
      },
      getModuleAuthor: function () {
        return this.splitModuleId()[0]
      },
      isModuleVersionAllowed: function () {
        const splitCoreVersion = coreLicense.coreVersion.split('.')
        const splitModuleVersion = this.getModuleInfo()['version']['major'].split('.')

        return splitCoreVersion[0] === splitModuleVersion[0]
          && splitCoreVersion[1] === splitModuleVersion[1]
      },
      handle: function (inputData, silent = false) {
        const self = this
        const url = xliteConfig.base_url

        return xcart.post(
            url + 'service.php/api/licenses',
            null,
            JSON.stringify(inputData),
            {
              contentType: 'application/json',
              dataType: 'json',
              success: license => {
                if (silent) {
                  return
                }

                self.license = license

                if (self.getMessage()) {
                  self.isError = true

                  xcart.trigger('message', {
                    type: 'error',
                    message: self.getMessage()
                  })

                  return false
                }

                if (self.isCoreLicense()) {
                  xcart.trigger('message', {
                    type: 'info',
                    message: xcart.t('X-Cart license key has been successfully verified')
                  })

                  location.reload()

                  return false
                }

                if (!self.isModuleVersionAllowed()) {
                  self.isError = true

                  xcart.trigger('message', {
                    type: 'error',
                    message: xcart.t('The "{{name}}" module version is incompatible with your core version and cannot be enabled.', {
                      name: self.getModuleName()
                    })
                  })

                  return false
                }

                xcart.trigger('message', {
                  type: 'info',
                  message: xcart.t('License key has been successfully verified and activated for "{{name}}" module by "{{author}}" author.', {
                    name: self.getModuleName(),
                    author: self.getModuleAuthor()
                  })
                })
              },
              error: xhr => {
                xcart.trigger('message', {
                  type: 'error',
                  message: xhr.responseJSON.detail
                })

                self.isError = true
              }
            })
          .then(function () {
            if (
              !silent
              && !self.isError
            ) {
              if (self.license.moduleSource === 'market') {
                location.href = `${url}service.php/market_module_installer?target=market_install_module&mainInstall=${self.license.moduleId}`
              } else {
                location.reload()
              }
            }
          })
          .then(successAction, failAction)
      }
    }
  }
})
