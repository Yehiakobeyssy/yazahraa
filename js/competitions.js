$(function() {

    // ------------------------
    // Hamburger Menu
    // ------------------------
    $(".menu-toggle").click(function(e) {
        e.stopPropagation();
        $(".main-menu ul").toggleClass("show");
    });

    $(document).click(function() {
        $(".main-menu ul").removeClass("show");
    });

    // ------------------------
    // Countdown Timer with Circular Clock
    // ------------------------
    const $timer = $('#timer');
    if ($timer.length) {

        let totalSeconds = parseInt($timer.data('time')) || parseInt($timer.text()) || 20;
        const nextUrl = $timer.data('next-url');

        // Create SVG circle if not exists
        if (!$timer.find('svg').length) {
            $timer.html(`
                <svg class="timer-svg" width="80" height="80">
                    <circle cx="40" cy="40" r="36" stroke="#ffffff" stroke-width="8" fill="none"/>
                    <circle cx="40" cy="40" r="36" stroke="#f44336" stroke-width="8" fill="none"
                            stroke-dasharray="226.194" stroke-dashoffset="0" transform="rotate(-90 40 40)"/>
                </svg>
                <div class="timer-text" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-weight:bold; font-size:18px; color:#fff;">
                    ${totalSeconds}
                </div>
            `);
        }

        const $circle = $timer.find('circle:nth-child(2)');
        const $text = $timer.find('.timer-text');
        const circumference = 2 * Math.PI * 36;
        $circle.css('stroke-dasharray', circumference);
        $circle.css('stroke-dashoffset', 0);

        let elapsed = 0;

        // Start with white
        $circle.css('stroke', '#ffffff');

        const timerInterval = setInterval(() => {
            elapsed++;
            const remaining = totalSeconds - elapsed;

            $text.text(remaining >= 0 ? remaining : 0);

            // Animate circle
            const offset = (elapsed / totalSeconds) * circumference;
            $circle.css('stroke-dashoffset', offset);

            // Blink effect: white/red each second
            if (elapsed % 2 === 0) {
                $circle.css('stroke', '#ffffff');
            } else {
                $circle.css('stroke', '#f44336');
            }

            if (remaining <= 0) {
                clearInterval(timerInterval);
                if (nextUrl) window.location.href = nextUrl;
            }
        }, 1000);

        // ------------------------
        // Option Buttons Click
        // ------------------------
        $(document).on('click', '.option-btn', function() {
            clearInterval(timerInterval);

            const $btn = $(this);
            const optionID = $btn.data('optionid');
            const isCorrect = $btn.data('correct');

            const $list = $('.list-group');
            const competitionID = $list.data('competitionid');
            const questionID = $list.data('questionid');
            const userID = $list.data('userid');

            // Get current index from URL
            const urlParams = new URLSearchParams(window.location.search);
            const currentIndex = parseInt(urlParams.get('index')) || 0;

            // Save answer via AJAX
            $.post('save_answer.php', {
                competitionID: competitionID,
                questionID: questionID,
                userID: userID,
                optionID: optionID,
                is_correct: isCorrect,
                first_question: currentIndex === 0 ? 1 : 0
            }, function() {
                // Highlight correct/incorrect
                $('.option-btn').each(function() {
                    const $opt = $(this);
                    if ($opt.data('correct') == 1) {
                        $opt.addClass('bg-success text-white');
                    } else if ($opt.data('optionid') == optionID) {
                        $opt.addClass('bg-danger text-white');
                    }
                    $opt.prop('disabled', true);
                });

                // Go to next question after 2 seconds
                setTimeout(() => {
                    if (nextUrl) window.location.href = nextUrl;
                }, 2000);
            });
        });

    }

});
