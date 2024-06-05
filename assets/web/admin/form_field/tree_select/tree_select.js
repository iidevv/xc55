xcart.microhandlers.add(
  'tree-select',
  '.input-field-wrapper.xlite-view-treeselect',
  function () {
    let $el = jQuery(this)
    let $view = $el.find('.tree-select-view')
    let $input = $el.find('.tree-select-input')

    $view.jstree({
      'plugins': $el.data('plugins'),
      'core': {
        'themes': $el.data('themes'),
        'data': $el.data('data')
      }
    })

    const handleClickInModifiedArrows = function(e) {
      if (e.offsetX > $(this).width()) {
        $(this).siblings('.jstree-icon.jstree-ocl').click();
        return false;
      }
    };
    const handleClickInAnchors = function(e, data) {
      $('.jstree-xcart > .jstree-no-dots .jstree-node > .jstree-anchor').unbind('click', handleClickInModifiedArrows).click(handleClickInModifiedArrows);
    };
    $view.on('ready.jstree', handleClickInAnchors);
    $view.on('open_node.jstree', handleClickInAnchors);
    $view.on('select_all.jstree', handleClickInAnchors);
    $view.on('deselect_all.jstree', handleClickInAnchors);

    $view.on('changed.jstree', function (e, data) {
      const selected = $view.jstree('get_bottom_selected')
      selected.sort()
      $input.val(selected).change()

      xcart.trigger('jstree.changed', {
        'el': $el,
        'data': data,
        'selected': selected
      })
    });

    $el.on('click', '.treeselect-select-all', (e) => {
      e.preventDefault();
      $view.jstree('select_all');
    });
    $el.on('click', '.treeselect-deselect-all', (e) => {
      e.preventDefault();
      $view.jstree('deselect_all');
    });
  }
)
