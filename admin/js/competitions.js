$(document).ready(function(){

    let questionCount = $("#questionsContainer .question-block").length;

    // إضافة سؤال جديد
    $("#addQuestionBtn").click(function(){
        // تحقق إذا كل الأسئلة الحالية محدد فيها الإجابة الصحيحة
        let allValid = true;
        $(".question-block").each(function(){
            if($(this).find("input[type='radio']:checked").length==0){
                alert("يجب اختيار إجابة صحيحة لكل سؤال قبل إضافة سؤال جديد!");
                allValid=false;
                return false;
            }
        });
        if(!allValid) return;

        questionCount++;
        let questionHtml = `
            <div class="question-block" data-qid="${questionCount}">
                <h4>السؤال ${questionCount}</h4>
                <input type="text" name="questions[${questionCount}][text]" class="form-control mb-2" placeholder="نص السؤال" required>
                
                <div class="optionsContainer" data-qid="${questionCount}"></div>

                <button type="button" class="btn btn-secondary addOptionBtn">إضافة احتمال</button>
                <button type="button" class="remove-question btn btn-danger">حذف السؤال</button>
                <hr>
            </div>
        `;
        $("#questionsContainer").append(questionHtml);
    });

    // حذف سؤال
    $(document).on("click", ".remove-question", function(){
        $(this).closest(".question-block").remove();
    });

    // إضافة احتمال واحد لكل سؤال
    $(document).on("click", ".addOptionBtn", function(){
        let qid = $(this).closest(".question-block").data("qid");
        let container = $(this).siblings(".optionsContainer");
        let optionCount = container.children().length + 1;

        let optionHtml = `
            <div class="option-block mt-1">
                <input type="text" name="questions[${qid}][options][${optionCount}][text]" placeholder="نص الاحتمال" required>
                <label>
                    <input type="radio" name="questions[${qid}][correct]" value="${optionCount}" required> الإجابة الصحيحة
                </label>
                <button type="button" class="remove-option btn btn-sm btn-danger">✖</button>
            </div>
        `;
        container.append(optionHtml);
    });

    // حذف الاحتمال
    $(document).on("click", ".remove-option", function(){
        $(this).closest(".option-block").remove();
    });

    // تحقق قبل الإرسال
    $("#editCompetitionForm").submit(function(){
        let valid = true;
        $(".question-block").each(function(){
            if($(this).find("input[type='radio']:checked").length==0){
                alert("يجب اختيار إجابة صحيحة لكل سؤال!");
                valid=false;
                return false;
            }
        });
        return valid;
    });

    $(".user-count").hover(
        function() { $(this).find(".user-list").show(); },
        function() { $(this).find(".user-list").hide(); }
    );
});
