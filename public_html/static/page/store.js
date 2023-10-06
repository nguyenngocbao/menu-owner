const MENU_URL = "/menu/";
const MENU = {
    id: {type:'text'},
    name : {type:'text'},
    sort : {type:'text'},
    store_id : {type:'text'},
    status: {type:'text'}
}

const ITEM_URL = "/item/";
const ITEM = {
    id: {type:'text'},
    name : {type:'text'},
    price : {type:'text'},
    sort : {type:'text'},
    menu_id : {type:'text'},
    status: {type:'text'}
}
$(window).load(function () {

    $('#edit-store-btn').click(function (){
        // let form = $('#menu-form');
        // const store_id = $('#store_id_filed').val();
        // let data = {
        //     id: 0,
        //     name : "",
        //     store_id: store_id,
        //     sort : 1,
        //     status: 1
        // }
        //
        // updateForm(MENU,form,data)
        $('#open-store-modal').click();
    })

    $('#add-menu-btn').click(function (){
        let form = $('#menu-form');
        const store_id = $('#store_id_filed').val();
        let data = {
            id: 0,
            name : "",
            store_id: store_id,
            sort : 1,
            status: 1
        }

        updateForm(MENU,form,data)
        $('#open-menu-modal').click();
    })


    $('.edit-menu-btn').click(function (){
        let form = $('#menu-form');
        const store_id = $('#store_id_filed').val();
        let data = {
            id: $(this).data('id'),
            name : $(this).data('name'),
            store_id: store_id,
            sort : 1,
            status: 1
        }

        updateForm(MENU,form,data)
        $('#open-menu-modal').click();
    })

    $('#add-menu').click(function (){
        let form = $('#menu-form');
        let data = getDataForm(MENU,form);


        $.ajax({
            method: 'POST',
            url:  MENU_URL + 'update',
            dataType: 'json',
            data: data,
            success: function (response) {
                console.log(response)
                if (response.err === 1) {
                } else {
                    location.reload();
                }
            },
            error: function (error) {
                console.log(error);
            }
        });

    })

    $('.delete-menu-btn').click(function (){

        let data = {'id': $(this).data('id')};

        $.ajax({
            method: 'POST',
            url:  MENU_URL + 'delete',
            dataType: 'json',
            data: data,
            success: function (response) {
                console.log(response)
                if (response.err === 1) {
                } else {
                    location.reload();
                }
            },
            error: function (error) {
                console.log(error);
            }
        });

    })



    $('.add-item-btn').click(function (){
        let form = $('#item-form');
        let data = {
            id: 0,
            name : "",
            menu_id: $(this).data("id"),
            price : 0,
            sort : 1,
            status: 1
        }
        updateForm(ITEM,form,data)
        $('#open-item-modal').click();
    })
    $('.edit-item-btn').click(function (){
        let form = $('#item-form');
        let data = {
            id: $(this).data('id'),
            name : $(this).data('name'),
            menu_id: $(this).data("menu_id"),
            price : $(this).data('price'),
            sort : 1,
            status: 1
        }
        updateForm(ITEM,form,data)
        $('#open-item-modal').click();
    })

    $('#add-item').click(function (){
        let form = $('#item-form');
        let data = getDataForm(ITEM,form);

        $.ajax({
            method: 'POST',
            url:  ITEM_URL + 'update',
            dataType: 'json',
            data: data,
            success: function (response) {
                console.log(response)
                if (response.err === 1) {
                } else {
                    location.reload();
                }
            },
            error: function (error) {
                console.log(error);
            }
        });

    })

    $('.delete-item-btn').click(function (){

        let data = {'id': $(this).data('id')};
        $.ajax({
            method: 'POST',
            url:  ITEM_URL + 'delete',
            dataType: 'json',
            data: data,
            success: function (response) {
                console.log(response)
                if (response.err === 1) {
                } else {
                    location.reload();
                }
            },
            error: function (error) {
                console.log(error);
            }
        });

    })



});
function clearForm(structure,form){

    for(let col in structure){
        let prop = structure[col];
        let name = col;
        switch (prop.type){
            case 'text':
            case 'date':
                $(form).find(`[name='${name}']`).val('');
                break;
            case 'image':
                $(form).find(`a[name='${name}']`).click();
                $(form).find(`img[name='${name}']`).attr('src',prop.default);
                break;
            case 'number':
                $(form).find(`[name='${name}']`).val(0);
                break;
            case 'selected':
                $(form).find(`[name='${name}']`).val(0).trigger('change');
                break;
            case 'checkbox':
                $(form).find(`[name='${name}']`).prop('checked',prop.default);
                break;
            case 'multiple-checkbox-text':
            case 'multiple-checkbox':
                values = [];
                $(form).find(`[name='${name}']:checked`).each(function() {
                    $(this).prop(false);
                });
                break;
        }
    }

}
function updateForm(structure,form,data){

    for(let col in structure){
        let prop = structure[col];
        let name = col;
        if (prop.updatedSkip){
            continue;
        }
        switch (prop.type){
            case 'text':
            case 'number':
                $(form).find(`[name='${name}']`).val(data[name]);
                break;
            case 'image':
                //$(form).find(`input[name='${name}']`).val(data[name]);
                $(form).find(`a[name='${name}']`).click();
                $(form).find(`img[name='${name}']`).attr('src',IMAGE_DOMAIN+data[name]);
                break;
            case 'selected':
                $(form).find(`[name='${name}']`).val(data[name]).trigger('change');
                break;
            case 'checkbox':
                $(form).find(`[name='${name}']`).prop('checked',1 == data[name]);
                break;
            case 'date':
                $(form).find(`[name='${name}']`).val(moment(data[name]).add(3, 'd').format('YYYY-MM-DD'));
                break;
            case 'multiple-checkbox-text':
                let values = data[name].trim().split(",");
                for (let value of values){
                    $(form).find(`[name='${name}'][value='${value}']`).prop('checked',true);
                };
                break;
            case 'multiple-checkbox':
                for (let value of data[name]){
                    $(form).find(`[name='${name}'][value='${value}']`).prop('checked',true);
                };
                break;
        }
    }

}
function getDataForm(structure,form){
    let result = {};
    for(let col in structure){
        result[col] = getInputParam(structure[col],col,form);
    }
    return result;
}

function getInputParam(properties,name,form){

    let  values = [];
    switch (properties.type){
        case 'text':
        case 'selected':
        case 'date':
        case 'number':
            return $(form).find(`[name='${name}']`).val();
        case 'checkbox':
            return $(form).find(`[name='${name}']`).is(':checked')? 1 : 0;
        case 'image':
            let file = $(form).find(`input[name='${name}']`)[0];
            if (file.files.length > 0){
                return file.files[0];
            }
            return '';
        case 'multiple-checkbox-text':
            values = [];
            $(form).find(`[name='${name}']:checked`).each(function() {
                values.push($(this).val());
            });
            return values.join(',');
        case 'multiple-checkbox':
            values = [];
            $(form).find(`[name='${name}']:checked`).each(function() {
                values.push($(this).val());
            });
            return values;
    }
}