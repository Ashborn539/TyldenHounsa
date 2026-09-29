$(document).ready(function() {
    $('.card').on('click', function(event) {
        if ($(event.target).closest('.btn-add-cart').length > 0) {
            return;
        }   // Si il y a 1 ou plusieurs element (>0) cliqué (event.target)
            // descendant d'un élément avec la classe .btn-add-cart, 
            // on ne fait rien et on quitte la fonction

        window.location.href = $(this).data('url');
        // Dans les attributs de l'element cliqué, on cherche l'attribut
        // data qui porte a sa suite le nom 'url' (data-url="...") et on change l'url
        // du navigateur pour aller vers cette url en utilisant la methode GET
    });
});
