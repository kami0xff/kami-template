{{--
    Engagement quiz — one question from front matter. The full text (question,
    options, explanation) is in the DOM, so crawlers index it; the JS only
    handles the reveal interaction. Works in static builds (inline script).
--}}
<section class="post-quiz" data-answer="{{ $quiz['answer'] }}">
    <h2>{{ __('Quick check') }}</h2>
    <p class="quiz-question">{{ $quiz['question'] }}</p>
    <div class="quiz-options" role="group" aria-label="{{ __('Answer options') }}">
        @foreach($quiz['options'] as $i => $option)
            <button type="button" class="quiz-option" data-index="{{ $i }}">{{ $option }}</button>
        @endforeach
    </div>
    @if(!empty($quiz['explanation']))
        <p class="quiz-explanation" hidden>{{ $quiz['explanation'] }}</p>
    @endif
</section>

@push('scripts')
<script>
document.querySelectorAll('.post-quiz').forEach(function (quiz) {
    var answer = parseInt(quiz.dataset.answer, 10);
    quiz.querySelectorAll('.quiz-option').forEach(function (btn) {
        btn.addEventListener('click', function () {
            quiz.querySelectorAll('.quiz-option').forEach(function (b) {
                b.disabled = true;
                b.classList.toggle('is-correct', parseInt(b.dataset.index, 10) === answer);
            });
            btn.classList.toggle('is-wrong', parseInt(btn.dataset.index, 10) !== answer);
            var explanation = quiz.querySelector('.quiz-explanation');
            if (explanation) explanation.hidden = false;
        });
    });
});
</script>
@endpush
