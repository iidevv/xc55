/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Login
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

const errorEffect = (elm) => {
  if (!elm) {
    return;
  }

  const inputBox = elm.closest('li.input');
  const elements = inputBox.children('div');
  const note = jQuery('<div class="login-box__error-note"/>');

  elements.wrapAll('<div class="input-effect-wrapper"/>');

  if (!inputBox.find('.login-box__error-note').length) {
    inputBox.append(note.html(elm.data('error')));
  }

  inputBox
    .addClass('has-error')
    .find('.input-effect-wrapper')
    .effect(
      'shake',
      { distance: 3 },
      function () {
        jQuery(this).children().unwrap();
      });
}

const clearFieldError = (elm) => {
  if (!elm) {
    return;
  }

  const inputBox = elm.closest('li.input');
  const note = inputBox.find('.login-box__error-note');

  inputBox.removeClass('has-error');

  if (note.length) {
    note.remove();
  }
}

xcart.microhandlers.add(
  'login-timer',
  '.login-box',
  function() {
    let timeLeft = jQuery(this).data('time-left');
    if (timeLeft) {
      (function() {
        timeLeft--;
        if (0 < timeLeft) {
          const min = parseInt(timeLeft / 60);
          const sec = timeLeft % 60;
          jQuery('#timer').text((10 > min ? '0' : '') + min +  ':' + (10 > sec ? '0' : '') + sec);
          setTimeout(arguments.callee, 1000);

        } else {
          jQuery('.login-box').removeClass('locked');
        }
      })()
    }
  }
);

xcart.microhandlers.add(
  'input',
  '[name="login"]',
  function(event) {
    const input = this;
    jQuery(function () {
      input.focus();
    })
  }
);

xcart.microhandlers.add(
  'login-box',
  '.login-box',
  function () {
    const box = jQuery(this);
    const inputs = box.find('ul.table').find('input');
    const submit = box.find('button[type=submit]');
    const login = box.find('.recover-password-form').length
      ? box.find('input[name=email]')
      : box.find('input[name=login]');
    const email = new CommonElement(login.get(0));

    inputs.each(function () {
      if (jQuery(this).val() !== '') {
        jQuery(this).closest('li.input').addClass('filled');
      }
    });

    if (!inputs.closest('.change-password-form-container').length) {
      inputs.on('input', function () {
        let notEmpty = [];

        inputs.each(function (index) {
          if (jQuery(this).val() !== '') {
            notEmpty.push(index);
          }
        });

        if (notEmpty.length === inputs.length) {
          submit.removeClass('disabled');
        } else {
          submit.addClass('disabled');
        }
      });
    }

    // Action button behavior
    inputs.on('blur', function () {
      const notEmpty = inputs.filter((i, item) => item.value !== '');

      if (jQuery(this).closest('span.input-field-wrapper').hasClass('has-error')) {
        jQuery(this).closest('li.input').addClass('has-error')
      } else {
        if (
          jQuery(this).attr('id') !== 'password-conf'
          && jQuery(this).attr('data-id') !== 'password-conf'
          && jQuery(this).closest('ul.table').data('errorType') !== 'PASSWORD_MATCHES_CURRENT'
        ) {
          clearFieldError(jQuery(this));
        }
      }

      if (notEmpty.length == inputs.length) {
        submit.removeClass('disabled');
      } else {
        submit.addClass('disabled');
      }
    });

    // Login input validation
    submit.on('click', function (e) {
      if (!email.validateEmail().status) {
        e.preventDefault();

        errorEffect(login);
      }
    });

    // Forgot password behavior
    if (box.find('.forgot-password').length) {
      const inp = box.find('input[name="login"]').eq(0);
      box.find('.forgot-password a').click(
        function(event) {
          if (inp.val()) {
            const link = jQuery(event.currentTarget);
            let url = new URL(link.attr('href'));

            url.searchParams.set('email', inp.val());

            link.attr('href', url.toString());
          }

          return true;
        }
      )
    }
  }
);

xcart.microhandlers.add(
  'change-password-submit',
  '.change-password-form-container',
  function () {
    const box = jQuery(this);
    const button = box.find('button.submit');
    const mainField = box.find('#password');

    button.addClass('disabled').prop('disabled', true);

    jQuery(document).on('input', '#password-conf, [data-id="password-conf"]', function() {
      const input = jQuery(this);

      if (input.val() !== mainField.val()) {
        input.closest('li.input').addClass('has-error');

        button.addClass('disabled').prop('disabled', true);
      } else {
        clearFieldError(input);

        button.removeClass('disabled').prop('disabled', false);
      }
    }).on('blur', '#password-conf, [data-id="password-conf"]', function () {
      const input = jQuery(this);

      if (input.val() !== mainField.val()) {
        button.addClass('disabled').prop('disabled', true);

        errorEffect(jQuery('#password-conf'));
      } else {
        clearFieldError(input);

        button.removeClass('disabled').prop('disabled', false);
      }
    });

    mainField.on('blur', function () {
      if (
        jQuery('#password-conf').val() !== ''
        && jQuery('#password-conf').closest('li.input').hasClass('has-error')
        && mainField.val() === jQuery('#password-conf').val()
      ) {
        clearFieldError(jQuery('#password-conf'));
        button.removeClass('disabled').prop('disabled', false);
      }
    })
  }
)

xcart.bind(
  'changePassword.error',
  function(event, data) {
    if (data.type === 'PASSWORD_MATCHES_CURRENT') {
      let error = true;
      const password = jQuery('#password');
      const confirmPassword = jQuery('#password-conf');

      if (password.length && error) {
        const oldValue = password.val();

        password
          .data('error', data.errorMessage)
          .closest('ul.table')
          .data('error-type', 'PASSWORD_MATCHES_CURRENT');

        errorEffect(password);

        jQuery(document).on('input', '#password, [data-id=password]', function () {
          if (jQuery(this).val() !== oldValue) {
            clearFieldError(password);

            password.closest('ul.table').removeData();

            error = false;
          }

          if (
            jQuery(this).val() !== jQuery('#password-conf').val()
          ) {
            errorEffect(confirmPassword);

            password
              .closest('form')
              .find('button.submit')
              .addClass('disabled')
              .prop('disabled', true);
          }

          if (
            jQuery(this).val() !== oldValue
            && jQuery(this).val() === jQuery('#password-conf').val()
          ) {
            clearFieldError(confirmPassword);

            password
              .closest('form')
              .find('button.submit')
              .removeClass('disabled')
              .prop('disabled', false);
          }
        });
      }
    }
  }
);

jQuery(document).on('focus', 'li.input .input-internal-wrapper input', function () {
  jQuery(this).closest('li.input').addClass('focused');
}).on('blur', 'li.input .input-internal-wrapper input', function () {
  jQuery(this).closest('li.input').removeClass('focused');

  if (jQuery(this).val() === '') {
    jQuery(this).closest('li.input').removeClass('filled');
  }
});

jQuery(document).on('input', 'li.input .input-internal-wrapper input', function () {
  if (jQuery(this).val() === '') {
    jQuery(this).closest('li.input').removeClass('filled');
  } else {
    jQuery(this).closest('li.input').addClass('filled');
  }
});
