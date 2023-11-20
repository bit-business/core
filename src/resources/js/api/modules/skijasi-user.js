import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/users";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/users/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    return resource.put(apiPrefix + "/v1/users/edit", data);
  },

  add(data) {
    return resource.post(apiPrefix + "/v1/users/add", data);
  },

  delete(data) {
    const paramData = {
      data: data,
    };
    return resource.delete(apiPrefix + "/v1/users/delete", paramData);
  },
  deleteMultiple(data) {
    const paramData = {
      data: data,
    };
    return resource.delete(apiPrefix + "/v1/users/delete-multiple", paramData);
  },
  roles(data = {}) {
    const ep = apiPrefix + "/v1/user-roles/all-role";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },
  addRoles(data) {
    return resource.post(apiPrefix + "/v1/user-roles/add-edit", data);
  },


  unapprovedAvatars(data = {}) {
    const ep = apiPrefix + "/v1/users/unapproved-avatars";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
},
approveAvatar(data) {
  return resource.put(apiPrefix + "/v1/users/approve-avatar", data);
},
declineAvatar(data) {
  return resource.put(apiPrefix + "/v1/users/decline-avatar", data);
},



totalUsers() {
  const url = apiPrefix + "/v1/users/count";
  return resource.get(url);
},

zadnjiidmember() {
  const ep = apiPrefix + "/v1/users/zadnjiidmember";
  const url = ep;
  return resource.get(url);
},


  browsenasiclanovi(data = {}) {
    const ep = apiPrefix + "/v1/users/browsenasiclanovi";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

};
