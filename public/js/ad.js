    $('#add-image').click(function (){
        //je récupére le numéro des futurs champs que je vais créer
        //const index = $('#ad_images div.form-group').length;
        const index = +$('widgets-counter').val();

        //je récupére le prototype des entrées
        const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);


        //injection du code au sein de la div
        $('#ad_images').append(tmpl);


        $('#widgets-counter').val(index + 1);

        //je gere le button supprimer
        handleDeleteButtons();
    });

    function handleDeleteButtons(){

        $('button[data-action="delete"]').click(function(){

            const target = this.dataset.target;
            $(target).remove();
        });
    }

    function updateCounter(){
        const count = +$('#ad_images dv.form-group').length;
        //fonction pour ne pas avoir de bug dans l'ajout/supp de photo 
        $('#widgets-counter').val(count);
    }
    updateCounter();
    handleDeleteButtons();
