$(document).ready(function () {
    let locationCount = 1;
    $('#addLocation').click(function (){
        let clone = $(this).closest('fieldset').clone(true);
        $(clone).find('legend').text('Destination '+ locationCount);
        $(clone).find('#addLocation')
            .replaceWith(`<button type="button" class="btn btn-danger btn-sm remove">
                            Remove <i class="fa fa-trash-alt"></i>
                        </button>`);
        $(clone).find('input, select').each(function () {
            $(this).val(this.defaultValue)
        });
        $('fieldset').last().after(clone);
        locationCount++;
    });

    $(document).on('click', '.remove', function () {
        $(this).closest('fieldset').remove();
    });

    $('.state').change(function () {
        let id = $(this).val();
        if (id){
            $(this).prev().val($(this).find('option:selected').text());
            let param = '?id='+id+'&op=Lga.getLgas';
            let nextDiv = $(this).parent().parent().next();
            let lgaNode = $(nextDiv).find('.lga');
            let postalCodeNode = $(nextDiv).next().find('.postalCode');
            $.ajax( {
                type: 'GET',
                url: 'utilities.php'+param,
                beforeSend: function () {
                    $(lgaNode)
                        .empty()
                        .append(`<option value="">Please wait ...</option>`)
                },
                success: function (response) {
                    response = jQuery.parseJSON(response);
                    $(lgaNode).prop("disabled", false);
                    if (response.responseCode === 200){
                        $(lgaNode).empty().append(`<option value="">:: SELECT LGA ::</option>`);
                        let data = response.data;
                        $(postalCodeNode).val(response.postalCode);
                        if (data.length > 0){
                            data.forEach((lga, index) => {
                                $(lgaNode).append(`<option value="${lga.name}">${lga.name}</option>`);
                            });
                        }
                    }else {
                        $(lgaNode).empty().append(`<option value="">Failed to fetch data</option>`);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    toastr.error('An Internal Server error occurred.');
                }
            });
        }
    });

    $('#searchForm').submit(function () {
        $('#search').prop('disabled', true)
            .empty()
            .append(`
                    <i class="fa fa-spinner fa-spin"></i>
                       Please wait...
                     `);
    });
});