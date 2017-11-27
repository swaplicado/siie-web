/** Clona un objeto (deep-copy)
 * @param  {Any}    from: el objeto a clonar
 * @param  {Object} dest: (opcional) objeto a extender
 * @return {Any} retorna el nuevo objeto clonado
 */
var fnClone = (function() {
  // @Private
  var _toString = Object.prototype.toString;

  // @Private
  function _clone (from, dest, objectsCache) {
    var prop;
    // determina si @from es un valor primitivo o una funcion
    if (from === null || typeof from !== "object") return from;
    // revisa si @from es un objeto ya guardado en cache
    if (_toString.call(from) === "[object Object]") {
      if (objectsCache.filter(function (item) {
        return item === from;
      }).length) return from;
      // guarda la referencia de los objetos creados
      objectsCache.push(from);
    }
    // determina si @from es una instancia de alguno de los siguientes constructores
    if (from.constructor === Date || from.constructor === RegExp || from.constructor === Function ||
      from.constructor === String || from.constructor === Number || from.constructor === Boolean) {
      return new from.constructor(from);
    }
    if (from.constructor !== Object && from.constructor !== Array) return from;
    // crea un nuevo objeto y recursivamente itera sus propiedades
    dest = dest || new from.constructor();
    for (prop in from) {
      // TODO: allow overwrite existing properties
      dest[prop] = (typeof dest[prop] === "undefined" ?
          _clone(from[prop], null, objectsCache) :
          dest[prop]);
    }
    return dest;
  }

  // función retornada en el closure
  return function (from, dest) {
    var objectsCache = [];
    return _clone(from, dest, objectsCache);
  };

}());
