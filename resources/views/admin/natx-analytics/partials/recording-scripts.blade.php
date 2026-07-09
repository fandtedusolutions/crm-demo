<script>
    $(document).on('click', '.js-toggle-recording', function () {
        const targetId = $(this).data('target');
        $('#' + targetId).toggle();
    });
</script>
