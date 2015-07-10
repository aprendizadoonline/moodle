// Embaralha os elementos de uma array.
// Autor: ChristopheD
// Removido de: http://stackoverflow.com/a/10142256
Array.prototype.shuffle = function() {
  var i = this.length, j, temp;
  if (i == 0) return this;
	
  while ( --i ) {
     j = Math.floor( Math.random() * ( i + 1 ) );
     temp = this[i];
     this[i] = this[j];
     this[j] = temp;
  }
  
  return this;
}

// Verifica se uma array contem um elemento
// Removido de: http://stackoverflow.com/a/237176
Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] === obj) {
            return true;
        }
    }
    return false;
}