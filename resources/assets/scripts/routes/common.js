export default {
  finalize() {
    $('#navbarCollapse')
      .on('show.bs.collapse', function () {
        document.body.classList.add('no-scroll');
      })
      .on('hidden.bs.collapse', function () {
        document.body.classList.remove('no-scroll');
      });

    // manage changing text on file upload
    $(document).on('change', '.custom-file-input', function () {
      let fileName = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
      $(this).parent('.custom-file').find('.custom-file-label').text(fileName);
    });
  },
};
