/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function ()
{

    // press on add question button
    $(document).on("click", ".addQuestion", function ()
    {
        var $question = $(".questions").find(".question").last().clone();

        var $questionNumber = $question.find(".input-group-addon").text().replace(/[^\d]/g, '');
        $question.find(".input-group-addon").text((parseInt($questionNumber) + 1) + ".");
        $question.find("input").val("");

        var $answers = $(".questions").find(".answers").last().clone();
        $answers.find(".answer").each(function ()
        {
            removeAnswer($(this));
        });

        $(".questions").find(".answers").last().after($question);
        $(".questions").find(".question").last().after($answers);
    });

    // press on delete question button
    $(document).on("click", ".removeQuestion", function ()
    {
        removeQuestion($(this));
    });

    // press on mulitple-choice checkbox
    $(document).on("change", ".multiple-choice", function ()
    {
        $(this).closest(".question").next(".answers").slideToggle(function ()
        {
            $(this).find(".answer").each(function ()
            {
                // toggle the required property as well
                $(this).find("input").prop('required', function (i, v)
                {
                    return !v;
                });
            });
        });
    });

    // press on add answer button
    $(document).on("click", ".addAnswer", function ()
    {
        var $answer = $(this).prev(".answer").clone();

        var $answerNumber = $answer.find(".input-group-addon").text().replace(/[^\d]/g, '');
        $answer.find(".input-group-addon").text((parseInt($answerNumber) + 1) + ".");
        $answer.find("input").val("");

        $(this).prev(".answer").after($answer);
    });

    // press on delete answer button
    $(document).on("click", ".removeAnswer", function ()
    {
        removeAnswer($(this));
    });

    $(document).on("click", ".btnReset", function ()
    {
        $(".questions").find(".answers").show(function ()
        {
            $(this).find(".answer").each(function ()
            {
                // toggle the required property as well
                $(this).find("input").prop('required', true);
            });
        });
    });
});

function removeQuestion(element)
{
    var $question = element.closest(".question");
    var $answers = $question.next(".answers");
    var countQuestions = $(".questions").find(".question").length;

    if (countQuestions == 1)
    {
        $question.find("input").val("");
        $answers.find(".answer").each(function ()
        {
            removeAnswer($(this));
        });
    }
    else
    {
        $question.remove();
        $answers.remove();
    }
    changeNumberSequence($(".questions"), "question");
}

function removeAnswer(element)
{
    var $parentAnswers = element.closest(".answers");
    var countAnswers = $parentAnswers.find(".answer").length;

    if (countAnswers == 1)
    {
        element.closest(".answer").find("input").val("");
    }
    else
    {
        element.closest(".answer").remove();
    }
    changeNumberSequence($parentAnswers, "answer");
}

function changeNumberSequence(parentDiv, type)
{
    var i = 1;
    parentDiv.find("." + type).each(function ()
    {
        $(this).find(".input-group-addon").text(i++ + ".");
    });
}

function getQuestionListCreateData()
{
    $form = $("#createQuestionListForm");

    // init array for inputvars
    var inputData = {};
    inputData["name"] = $form.find("#input-Name").val(); // get name for the list

    // init an array to put the questions in
    var aQuestions = [];
    // loop through each question
    $form.find(".question").each(function ()
    {
        // get question
        var question = $(this).find(".input-question").val();

        // is it a multiple choice question?
        var multipleChoice = $(this).find('.multiple-choice').is(":checked");
        if (multipleChoice)
        {
            var aAnswers = [];
            // loop through all answers
            $(this).next(".answers").find(".answer").each(function ()
            {
                aAnswers.push(
                        {
                            answer: $(this).find(".input-answer").val(),
                            points: $(this).find(".input-points").val()
                        }
                );
            });
            // add to the questions array
            aQuestions.push({question: question, answers: aAnswers});
        }
        else
        {
            // add to the questions array
            aQuestions.push({question: question, answers: null});
        }
    });
    // add questions to the inputData
    inputData["questions"] = aQuestions;
    // return inputData
    return inputData;
}

function getQuestionListFillInData()
{
    $form = $("#fillInQuestionListForm");

    // init array for inputvars
    var inputData = {};
    // get necessary params
    inputData["measurementID"] = $form.data("measurementid");
    inputData["userID"] = $form.data("userid");

    // init an array to put the questions in
    var aQuestions = [];
    // loop through each multiple choice question
    $form.find(".multiple-choice").each(function ()
    {
        // get parent
        var parent = $(this).parent();
        var answers = parent.find("input[type='radio']:checked");
        // check if any radio is checked
        if (answers.val())
        {
            // get values we need
            var answer = answers.val();
            var questionID = answers.data("questionid");
            var tableColumn = answers.data("column");
            var rowid = null;
            if(answers[0].hasAttribute("data-rowid"))
            {
                rowid = answers.data("rowid");
            }
            // add to the questions array
            aQuestions.push(
                    {
                        questionID: questionID,
                        column: tableColumn,
                        answer: answer,
                        rowid: rowid
                    }
            );
        }
    });
    // loop through each open question
    $form.find(".open-question").each(function ()
    {
        // get parent
        var parent = $(this).parent();
        var answers = parent.find("input");
        // check if an answer has been provided
        if (answers.val())
        {
            var answer = answers.val();
            var questionID = answers.data("questionid");
            var tableColumn = answers.data("column");
            var rowid = null;
            if(answers[0].hasAttribute("data-rowid"))
            {
                rowid = answers.data("rowid");
            }

            // add to the questions array
            aQuestions.push(
                    {
                        questionID: questionID,
                        column: tableColumn,
                        answer: answer,
                        rowid: rowid
                    }
            );
        }
    });
    // add questions to the inputData
    inputData["questions"] = aQuestions;
    // return inputData
    return inputData;
}