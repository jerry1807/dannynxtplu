/**
 * Analyzer Namespane
 */
let Analyzer = {};

Analyzer.DatePicker = function()
{
    $(".js-analyzer-datepicker").each(function() {
        $(this).removeClass("js-datepicker");

        if ($(this).data("max-date")) {
            $(this).data("max-date", new Date($(this).data("max-date")))
        }

        if ($(this).data("start-date")) {
            $(this).data("start-date", new Date($(this).data("start-date")))
        }

        let minDate = false;
        if ($(this).data("min")) {
            minDate = new Date($(this).data("min"));
        }

        $(this).datepicker({
            language: $("html").attr("lang"),
            dateFormat: "yyyy-mm-dd",
            timeFormat: "hh:ii",
            autoClose: true,
            timepicker: false,
            toggleSelected: false,
            minDate: minDate
        });
    });


    $('#dateSubmit').on('click', function(event)
    {
        let dateStart = $("input[name='dateStart']").val();
        let dateEnd = $("input[name='dateEnd']").val();
        let baseUrl = $("input[name='baseUrl']").val();

        // Redirect
        window.location.href = baseUrl + "/" + dateStart + "/" + dateEnd;

        event.preventDefault();
    });
}
