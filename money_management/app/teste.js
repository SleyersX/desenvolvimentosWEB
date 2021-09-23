const teste = "CART\u00c3\u0083O DE CR\u00c3\u0089DITO";
//const teste = "CART\u00c3O";
console.log(decodeURIComponent(teste));
console.log(encodeURI('\uD800\uDFFF'));
console.log(decodeURI('%F0%90%8F%BF'));