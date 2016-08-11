(function ($) {

    $(".sptp-fieldset").each(function () {

        var $fieldset = $(this);
        var $customInput = $fieldset.find('input[type=text]');
        var $select = $fieldset.find('input[type=radio]');
        $select.change(function () {
            var val = $(this).val();
            if (val != 'custom') {
                $customInput.val(val);
            }

        });

        $customInput.on('focus', function () {
            $select.prop("checked", false);
            $fieldset.find('input[value=custom]').prop("checked", true);

        });
    });

}(jQuery));

(function ($) {

    var $permalink_structure = $("#permalink_structure");
    var $selection = $("input[name=selection]");

    function setFront(val) {
        var front = val.split("%").shift();
        $(".front").html(front.substr( 0, front.length-1 ) );
    }

    function setSlash(val) {
        var last = val.slice(-1);
        if( last == "/") {
            $(".slash").html(last);
        }
        else {
            $(".slash").html(' ');
        }
    }

    $selection.change(function () {
        var val = $(this).val();
        if (val == 'custom') {
            val = $permalink_structure.val();
        }
        setFront(val);
        setSlash(val);
    });

    $permalink_structure.on('keyup change', function () {
        var val = $permalink_structure.val();

        setFront(val);
        setSlash(val);
    });


}(jQuery));