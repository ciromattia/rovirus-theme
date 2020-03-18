import $ from 'jquery';

/* global script_vars */
function handler($element) {
  jQuery.ajax({
    url: script_vars.AJAXurl,
    type: 'POST',
    dataType: 'html',
    data: {
      'action': 'svg_get_attachment_url',
      'attachmentID': $element.attr('data-id'),
    },
    success: function (data) {
      if (data) {
        $element.find('img').attr('src', data);
        $element.find('.filename').text('SVG');
      }
    },
  });
}

const mediaGridObserver = new MutationObserver(function (mutations) {
  for (let i = 0; i < mutations.length; i++) {
    for (let j = 0; j < mutations[i].addedNodes.length; j++) {
      const $element = $(mutations[i].addedNodes[j]);
      if ($element.attr('class')) {
        if ($element.attr('class').indexOf('attachment') !== -1) {
          const $attachmentPreview = $element.children('.attachment-preview');
          if ($attachmentPreview.length !== 0) {
            if ($attachmentPreview.attr('class').indexOf('subtype-svg+xml') !== -1) {
              handler($element);
            }
          }
        }
      }
    }
  }
});

const attachmentPreviewObserver = new MutationObserver(function (mutations) {
  for (let i = 0; i < mutations.length; i++) {
    for (let j = 0; j < mutations[i].addedNodes.length; j++) {
      const $element = $(mutations[i].addedNodes[j]);
      let onAttachmentPage = false;
      if (($element.hasClass('attachment-details')) || $element.find('.attachment-details').length != 0) {
        onAttachmentPage = true;
      }

      if (onAttachmentPage === true) {
        const urlLabel = $element.find('label[data-setting="url"]');
        if (urlLabel.length !== 0) {
          const value = urlLabel.find('input').val();
          $element.find('.details-image').attr('src', value);
        }
      }
    }
  }
});

$(document).ready(function () {
  mediaGridObserver.observe(document.body, {
    childList: true,
    subtree: true,
  });

  attachmentPreviewObserver.observe(document.body, {
    childList: true,
    subtree: true,
  });
});
