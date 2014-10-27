
$(function(){
 
    $('#task-preset-content').hide();
    $('#task-preset-slide-up').hide();
    
    $('#task-preset-slide-down').click(function(event){
        $('#task-preset-content').show();
        $('#task-preset-slide-up').show();
        $('#task-preset-slide-down').hide();
    });

    $('#task-preset-slide-up').click(function(event){
        $('#task-preset-content').hide();
        $('#task-preset-slide-down').show();
        $('#task-preset-slide-up').hide();
    });

    $('#project_form_endDateOnLastTask').click(function(event){
        $('#project_form_endDate').val("");
    });

    $('#project_form_endDate').click(function(event){
        $('#project_form_endDateOnLastTask').prop( "checked", false );
    });
    
    if ($('#project_form_endDateOnLastTask').is(':checked')) {
        $('#project_form_endDate').val("");
    }else if ( $('#project_form_endDate').val() != ''){
        $('#project_form_endDateOnLastTask').prop( "checked", false );
    }

    $("#project_form_accountProfile").change(function(){

        if( $(this).val() != '' ){
            
            var ajaxPath = '../ajax/';

            if (projectId == 0 ){
                ajaxPath = 'ajax/';
            }

            ajaxPath += $(this).val() + '/' + projectId;

            $.ajax
            ({
                type: "POST",
                data: "data=" + $(this).val(),
                url: ajaxPath,
                cache: false,
                success: function(data){
                    $.each(data, function(key, text){
                        $('#project_form_opportunity').append(
                            $('<option></option>').val(parseInt(key)).html(text)
                        );
                    });
                }
            });
        }else{
            $("#project_form_opportunity").html("<option value>Choose an option</option>");
        }
    });

    if ( projectId == 0 ){
        $("#project_form_opportunity").html("<option value>Choose an option</option>");
    }
});

function showHideTr(className, mode)
{
    var tr = document.getElementsByClassName(className);

    for(var i = 0; i < tr.length; i++){
        if (mode == 'show'){
            tr[i].style.display = 'table-row';
        }else{
            tr[i].style.display = 'none';
        }
    }

    if (mode == 'show'){
        document.getElementById('show-'+className).style.display = 'none';
        document.getElementById('hide-'+className).style.display = 'block';
    }else{
        document.getElementById('show-'+className).style.display = 'block';
        document.getElementById('hide-'+className).style.display = 'none';
    }
}
