export default {
  module: {
    singular: 'Mtr',
    plural: 'Mtrs',
    url: 'mtrs',
    icon: 'fa fa-align-justify',
    btn: {
      new: 'Novo Mtr',
      edit: 'Editar o Mtr',
      del: 'Excluir Mtrs'
    }
  },
  filter: '',
  item: {},
  list: [],
  currentId: '',
  loading: true,
  tipo_efluente: [],
  MtrGerado: [
    {
      label: 'Próprio',
      value: 'P'
    },
    {
      label: 'Terceiros',
      value: 'T'
    }
  ],
  MtrGerarCertificado: [
    {
      label: 'Sim',
      value: 'S'
    },
    {
      label: 'Não',
      value: 'N'
    }
  ]
}
