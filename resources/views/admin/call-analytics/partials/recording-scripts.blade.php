<script>
    function pauseOtherRecordings(activeId) {
        document.querySelectorAll('.call-recording-player').forEach(function (player) {
            if (player.id !== activeId) {
                player.style.display = 'none';
                const audio = player.querySelector('audio');
                if (audio) {
                    audio.pause();
                    audio.currentTime = 0;
                }
            }
        });
    }

    $(document).on('click', '.js-toggle-recording', function () {
        const targetId = $(this).data('target');
        const player = document.getElementById(targetId);
        if (!player) return;

        const isHidden = player.style.display === 'none' || player.style.display === '';
        pauseOtherRecordings(targetId);

        if (isHidden) {
            player.style.display = 'block';
            const audio = player.querySelector('audio');
            if (audio) audio.play().catch(function () {});
        } else {
            player.style.display = 'none';
            const audio = player.querySelector('audio');
            if (audio) audio.pause();
        }
    });
</script>
