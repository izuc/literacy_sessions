$.fn.serializeForm = function() {
   var object = {};
   var elements = this.serializeArray();
   $.each(elements, function() {
       if (object[this.name]) {
           if (!object[this.name].push) {
               object[this.name] = [object[this.name]];
           }
           object[this.name].push(this.value || '');
       } else {
           object[this.name] = this.value || '';
       }
   });
   return object;
};