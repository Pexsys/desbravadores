jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "ptbr-string-asc" : function (s1, s2) {
        return s1.localeCompare(s2);
    },
    "ptbr-string-desc" : function (s1, s2) {
        return s2.localeCompare(s1);
    }
});

jQuery.fn.DataTable.ext.type.search.string = function ( data ) {
    return ! data ?
        '' :
        typeof data === 'string' ?
            data
                .replace( /έ/g, 'ε')
                .replace( /ύ/g, 'υ')
                .replace( /ό/g, 'ο')
                .replace( /ώ/g, 'ω')
                .replace( /ά/g, 'α')
                .replace( /ί/g, 'ι')
                .replace( /ή/g, 'η')
                .replace( /\n/g, ' ' )
                .replace( /[áÁ]/g, 'a' )
                .replace( /[éÉ]/g, 'e' )
                .replace( /[íÍ]/g, 'i' )
                .replace( /[óÓ]/g, 'o' )
                .replace( /[úÚ]/g, 'u' )
                .replace( /[âÂ]/g, 'a' )
                .replace( /[êÊ]/g, 'e' )
                .replace( /[îÎ]/g, 'i' )
                .replace( /[ôô]/g, 'o' )
                .replace( /[ûÛ]/g, 'u' )
                .replace( /[àÀ]/g, 'a' )
                .replace( /[èÈ]/g, 'e' )
                .replace( /[ìÌ]/g, 'i' )
                .replace( /[òÒ]/g, 'o' )
                .replace( /[ùÙ]/g, 'u' )
                .replace( /[äÄ]/g, 'a' )
                .replace( /[ëË]/g, 'e' )
                .replace( /[ïÏ]/g, 'i' )
                .replace( /[öÖ]/g, 'o' )
                .replace( /[üÜ]/g, 'u' )
                .replace( /[ãÃ]/g, 'a' )
                .replace( /[õÕ]/g, 'o' )
                .replace( /[çÇ]/g, 'c' ) :
            data;
};