(function($){
    $(".sptp-fieldset").each(function(){

        var $fieldset = $(this);
        var $customInput = $fieldset.find('input[type=text]');
        $fieldset.find('input[type=radio]').change(function() {
            var val = $(this).val();
            if( val != 'custom') {
                $customInput.val( val );
            }

        });
    });




}(jQuery));

(function($){

    var $permalink_structure = $("#permalink_structure");
    var $selection = $("input[name=selection]");

    function setFront(val) {
        var front = val.split("%").shift().substr(1);
        $(".front").html(front);
    }

    $selection.change(function(){
        var val = $(this).val();
        if(val == 'custom') {
            val = $permalink_structure.val();
        }
        setFront(val);
    });

    $permalink_structure.on('keyup change', function(){
        setFront($permalink_structure.val());
    });


}(jQuery));