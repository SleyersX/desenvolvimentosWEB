errors = [];
fields = [];

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se a estrutura do string contém caracteres especiais
 */
 function isSpecialCharacter(str){
    
    let reg = /^(?=.*[ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝŔÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿŕ@!#$%^~´`¨&*()/\\])[ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝŔÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿŕ@!#$%^~´`¨&*()/\\a-zA-Z0-9]+$/;
    str = str.replace(/\s+/g, '');
    return reg.test(str);

}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se a estrutura do string contém caracteres maiuscula e númerico
 */
function validaEstrutura(str){
    
    let reg = /^(?=.*[ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝŔÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿŕa-z@!#$%^~´`¨&*()/\\])[ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝŔÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿŕa-z@!#$%^~´`¨&*()/\\A-Z0-9]+$/;
    str = str.replace(/\s+/g, '');
    return reg.test(str);

}


/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se a estrutura do string contém  númerico
 */
function validaStringNumerica(str){

    let reg = /^(?=.*[a-zA-Z@!#$%^&*()-/\\])[a-zA-Z@!#$%^&*()-/\\0-9]+$/;
    return reg.test(str);

}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se o valor é null
 */
 function isNull(str){
    return str === null;
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se o valor é null
 */
function isUndefined(str){
    return str === undefined;
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se a estrutura do string contém caracteres
 */
function isBlank(str) {
    return !str || /^\s*$/.test(str);
}
  
/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se o valor passado é numerico
 */
function isNumber(str) {
    return typeof str === "number";
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se o email passado possui uma estrutura valida
 */
function IsEmail(email) {
    let er = new RegExp(
        /^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/
    );
    return !(email === "" || !er.test(email));

}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se o valor passado é integer
 */
function isInteger(str) {
  return isNumber(str) && Number.isInteger(str);
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se o valor passado é string
 */
function isString(str) {
  return typeof str === "number";
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se o valor passado é um array
 */
function isArray(str) {
  return str.constructor === Array;
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se o valor passado é um array vazio
 */
function checkArrayIsEmpty(str) {
  return isBlank(str) || !isArray(str) || str.length === 0;
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida maximo e minimo caracteres para o campo
 */
function checkMaxMinLength(str, min, max){
    str = str.toString();
    if(str.length < min){
        return false;
    } else return str.length <= max;
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se o valor passado existe dentro do array listUF
 */
function checkUF(str){
    let listaUF = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RR', 'RO', 'RJ', 'RN', 'RS', 'SC', 'SP', 'SE', 'TO'];
    let pos = listaUF.indexOf(str);
    return pos >= 0;
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função valida se o campo é obrigatório validação
 */
function checkCamposObrigatorios(str){
    let required = [
        'body',
        'proposta',
        'numeroCalculo'
    ];
    return required.indexOf(str);
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função converte o valor passado para double
 */
function pushFieldsArray(field){
    if(fields.indexOf(field) <= 0){
        fields.push(field);
    }
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função converte o valor passado para double
 */
function toDouble(valor){
    valor = parseFloat(valor);
    return valor;
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 * Esta função testa se o valor passado é um data DD/MM/YYYY
 */
function testaData(param) {
    return param instanceof Date && !isNaN(param);
    //let result = moment(param, 'MM/DD/YY',true).isValid();
   // console.log(result);
  //return true;
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
function validarEmail(campo, field) {
    if (isBlank(field)) {
        pushFieldsArray(campo);
        errors.push("O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
        return false;
    } else if (!checkMaxMinLength(field,5,200)) {
        pushFieldsArray(campo);
        errors.push("Tamanho excedido, o campo " + campo + " pode conter no máximo 200 caracteres.");
        return false;
    } else if (!IsEmail(field)) {
        pushFieldsArray(campo);
        errors.push("O campo " + campo + " é inválido, revise o payload.");
        return false;
    }
    return true;
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
 function validarNumeroCalculo(campo, field){
    if(checkCamposObrigatorios(campo) >= 0) {
        if (isBlank(field)) {
            pushFieldsArray(campo);
            errors.push("O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if(!isInteger(field)){
            pushFieldsArray(campo);
            errors.push("O campo " + campo + " deve ser do tipo integer.");
            return false;
        }
    }
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
function validarCamposTipoString(campo, field){
    if(checkCamposObrigatorios(campo) >= 0 || !isBlank(field)){
        if (isBlank(field)) {
            pushFieldsArray(campo);
            errors.push("O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if(isSpecialCharacter(field)){
            pushFieldsArray(campo);
            errors.push("Atenção, revisar campo " + campo + " que contém palavras acentuadas e ou caracteres especiais.");
            return false;
        }
    }
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
function validarSusepCorretorSecundario(campo, field){
    if(checkCamposObrigatorios(campo) >= 0 || !isBlank(field)) {
        if (isBlank(field)) {
            pushFieldsArray(campo);
            errors.push("O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if (!checkMaxMinLength(field, 6, 6)) {
            pushFieldsArray(campo);
            errors.push(
            "O campo " + campo + " é obrigatório e deve conter apenas letras maiusculas e números totalizando 6 caracteres."
            );
            return false;
        }
        if(validaEstrutura(field)){
            pushFieldsArray(campo);
            errors.push("Atenção, revisar campo " + campo + " que contém palavras acentuada, minusculas ou caracteres especiais.");
            return false;
        }
    }
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
function validarEstado(campo, field){
    if(checkCamposObrigatorios(campo) >= 0 || !isBlank(field)) {
        if (isBlank(field)) {
            pushFieldsArray(campo);
            errors.push("O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if(isNull(field)){
            pushFieldsArray(campo);
            errors.push("O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if (!checkUF(field)){
            pushFieldsArray(campo);
            errors.push("O campo " + campo + " não possui o valor correto.");
            return false;
        }
    }
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
 function validarEstadoArray(arrayName, campo, index, field){
    if(checkCamposObrigatorios(campo) >= 0 || !isBlank(field)) {
        if (isBlank(field)) {
            pushFieldsArray(arrayName + "[" + index + "]." + campo);
            errors.push("O campo " + arrayName + "[" + index + "]." + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if(field === undefined){
            pushFieldsArray(arrayName + "[" + index + "]." + campo);
            errors.push("O campo " + arrayName + "[" + index + "]." + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if (!checkUF(field)){
            pushFieldsArray(arrayName + "[" + index + "]." + campo);
            errors.push("O campo " + arrayName + "[" + index + "]." + campo + " não possui o valor correto.");
            return false;
        }
    }
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
function validarCamposEndereco(campo, field){
    if(checkCamposObrigatorios(campo) >= 0 || !isBlank(field)){
        if (isBlank(field)) {
            pushFieldsArray(campo);
            errors.push("O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if(isSpecialCharacter(field)){
            pushFieldsArray(campo);
            errors.push("Atenção, revisar campo " + campo + " contém palavras acentuadas e ou caracteres especiais.");
            return false;
        }
    }
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
 function validarCamposEnderecoArray(arrayName, campo, index, field){
    if(checkCamposObrigatorios(campo) >= 0 || !isBlank(field)){
        if (isBlank(field)) {
            pushFieldsArray(arrayName + "[" + index + "]." + campo);
            errors.push("O campo " + arrayName + "[" + index + "]." + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if(isSpecialCharacter(field)){
            pushFieldsArray(arrayName + "[" + index + "]." + campo);
            errors.push("Atenção, revisar campo " + arrayName + "[" + index + "]." + campo + " contém palavras acentuadas e ou caracteres especiais.");
            return false;
        }
    }
}

function validarObjetosJson(campo, objeto){
    if(checkCamposObrigatorios(campo) >= 0){
        if(typeof objeto === "undefined"){
            pushFieldsArray(campo);
            errors.push("1 - O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if(isNull(objeto)){
            pushFieldsArray(campo);
            errors.push("2 - O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
        if(isBlank(objeto)){
            pushFieldsArray(campo);
            errors.push("3 - O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
            return false;
        }
    }
    if(!isBlank(objeto)){
        return true;
    }
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
function validarEstruturaProposta(campo, field){
    if(isArray(field)){
        pushFieldsArray(campo);
        errors.push("Não é possível enviar mais de uma cotação.");
        return false;
    }else {
        validarEmail("email", field.email);

        validarCamposTipoString("tipoFormaPagamento", field.tipoFormaPagamento);

        validarCamposTipoString("enviarEmailPara", field.enviarEmailPara);

        validarSusepCorretorSecundario("susepCorretorSecundario", field.susepCorretorPrincipal);
    }
}

/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
 function validarEstruturaBeneficiario(campo, field){
    if(isArray(field)){
        let sizeArray = field.length;
        for (let i = 0; i < sizeArray; i++) {
            validarEstadoArray("beneficiario", "estado", i, field[i].estado);
            validarCamposEnderecoArray("beneficiario", "tipoLogradouro", i, field[i].tipoLogradouro);
            validarCamposEnderecoArray("beneficiario","logradouro", i, field[i].logradouro);
            validarCamposEnderecoArray("beneficiario", "bairro", i, field[i].bairro);
            validarCamposEnderecoArray("beneficiario", "cidade", i, field[i].cidade);
        }
    }else {
        validarEstado("estado", field.estado);
        validarCamposEndereco("tipoLogradouro", body.proposta.beneficiario.tipoLogradouro);
        validarCamposEndereco("logradouro", body.proposta.beneficiario.logradouro);
        validarCamposEndereco("bairro", body.proposta.beneficiario.bairro);
        validarCamposEndereco("cidade", body.proposta.beneficiario.cidade);
    }
}

function validationBody(campo){
     if(typeof body === "undefined"){
         pushFieldsArray(campo);
         errors.push("O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
         return false;
     }
    if(isNull(body)){
        pushFieldsArray(campo);
        errors.push("O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
        return false;
    }
    if(isBlank(body)){
        pushFieldsArray(campo);
        errors.push("O campo " + campo + " é obrigatório e não pode ser nulo ou vazio, revise o payload.");
        return false;
    }
    return true;
}
/**
 * Card: 27179 "Ajustes para entrar em produção (API Cotação)" Autor: Walter Moura
 */
function validarCampos() {
    //console.log(validationBody("body"));
    //console.log(validarObjetosJson("body", body));
    if(validationBody("body")){
        validarNumeroCalculo("numeroCalculo", body.numeroCalculo);
        if(validarObjetosJson("proposta", body.proposta)){
            validarEstruturaProposta("proposta",body.proposta);
            if(validarObjetosJson("beneficiario",body.proposta.beneficiario)){
                validarEstruturaBeneficiario("beneficiario", body.proposta.beneficiario);
            }
        }
    }
}

body = {
    "numeroCalculo": 123456,
    "proposta": {
        "estadoCivil": "1",
        "email": "leof..neres@gmail.com",
        "tipoTelefone": "3",
        "numeroCelular": "9996492044",
        "numeroTelefoneProprietario": null,
        "numeroTelefoneResidencial": null,
        "susepCorretorPrincipal": "54454J",
        "porcentagemCorretorPrincipal": "100",
        "nacionalidade": null,
        "enviarEmailPara": "TODOS",
        "socios": 0,
        "flagSexo": null,
        "residePais": null,
        "pessoaExpostaPoliticamente": null,
        "valorCustoApolice": 0,
        "valorEncargos": 0,
        "valorIOF": 30.45,
        "valorJuros": 0,
        "quantidadeParcelas": 1,
        "codigoFormaPagamento": 21,
        "valorPrimeiraParcela": 443.08,
        "valorDemaisParcelas": 0,
        "tipoFormaPagamento": "1 x  1X FATURA MENSAL SEM ENTRADA",
        "beneficiario": [
            {
                "tipoBeneficiario": "1",
                "cpf": "08771038043",
                "cnpj": "",
                "cep": "01204000",
                "tipoLogradouro": "R",
                "logradouro": "GUAIANASES",
                "numeroLogradouro": "5",
                "complementoLogradouro": "Ap 503",
                "bairro": "CAMPOS ELISÍOS",
                "cidade": "SAO PAULO",
                "estado": "SP"
            },
            {
                "tipoBeneficiario": "1",
                "cpf": "08771038043",
                "cnpj": "",
                "cep": "01204000",
                "tipoLogradouro": "R",
                "logradouro": "GUAIANASES",
                "numeroLogradouro": "5",
                "complementoLogradouro": "Ap 503",
                "bairro": "CAPAO REDONDO",
                "cidade": "SAO PAULO",
                "estado": "SP"
            }]
    }
};

let data = new Date("2021-08-28");

console.log(data + " - " + testaData(data));
validarCampos();

output = {
    fields: fields,
  errors: errors,
};

console.log(output);