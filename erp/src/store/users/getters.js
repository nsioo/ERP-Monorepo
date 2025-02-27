import _ from 'lodash'
export const roles = (state) => {
  const user = state.currentUser
  if (user) {
    user.roles.push(user.role)
    return user.roles
  }
  return []
}

export const permissions = (state) => {
  return _.groupBy(_.uniqBy(_.flatten(roles(state).map(role => role.permissions)), 'key'), 'table_name')
}

export const module = (state) => {
  return state.module
}

export const list = (state) => {
  return state.list
}

export const item = (state) => {
  return state.item
}

export const loading = (state) => {
  return state.loading
}

export const filter = (state) => {
  return state.filter
}

export const pagination = (state) => {
  return state.pagination
}

export const currentId = (state) => {
  return state.currentId
}
