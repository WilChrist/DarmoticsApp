function deletee(id, meaningValue) {
    $(document).ready(function () {
        //$('#supprform').attr('action',"/EmployeeC/"+id+"/delete");

        $('#id').val(id);
        $('#meaningvalue').html("<b class='badge badge-warning'>" + meaningValue + "</b>");
        $('#myModal').modal('show');
    });
};
$(function () {
    const RACINE="";
    $("#suppBout").click(function () {
        console.log($("#id").val());
        console.log($('#controller').val());
        $.ajax({
            url: RACINE+"/" + $('#controller').val() + "/" + $("#id").val() + "/delete",
            method: "POST",
            data: {"id": $("#id").val(), "reason": $("#reason").val()}

        })
            .done(function (data) {
                //console.log($("#reason").val());
                console.log(JSON.parse(data));
                $('#' + $("#id").val()).remove();
                $('#myModal').modal('hide');
                if (JSON.parse(data).great == "1") {
                    $('#mess').attr('class', 'alert alert-success alert-dismissible');
                } else {
                    $('#mess').attr('class', 'alert alert-warning alert-dismissible');
                }

                $('#mess').html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong>' + JSON.parse(data).message + '</strong>');
            })
            .fail(function (data) {
                $('#myModal').modal('hide');
                $('#mess').attr('class', 'alert alert-danger alert-dismissible');
                $('#mess').html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong>' + 'Erreur de communication avec le Serveur, Veuillez reéssayer et si l\'erreur persiste merci de contacter votre Webmaster' + '</strong>');
            })
    })
});