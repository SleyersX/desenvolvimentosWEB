const ModeloTabela = require('../rotas/fornecedores/ModeloTabelaFornecedores')

ModeloTabela
    .sync()
    .then(() => console.log('Tabela criado com sucesso!'))
    .catch(console.log)