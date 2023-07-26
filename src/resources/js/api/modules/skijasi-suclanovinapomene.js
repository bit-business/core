import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";



export default {


 citanje(params ) {
  
    const ep = apiPrefix + "/v1/entities/su-clanovinapomene/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },
  

read(data) {
  const ep = apiPrefix + "/v1/entities/su-clanovinapomene/read";
  const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-clanovinapomene";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  all(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-clanovinapomene" + "/all";
    const url = ep;
    return resource.get(url);
  },


  readBySlug(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-clanovinapomene/read?slug=su-clanovinapomene&idmember={idmember}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/su-clanovinapomene/edit", { data: data });

  //  return resource.put(apiPrefix + "/v1/entities/su-clanovinapomene/edit", data);
  },

  update(data = []) {
  const ep = apiPrefix + "/v1/entities/su-clanovinapomene/update";
  return resource.put(ep, data);
},


add(data) {

  if (!data.paydate || data.paydate.trim() === '') {
    data.paydate = null;
  }
  if (!data.opendate || data.opendate.trim() === '') {
    data.opendate = null;
  }
  return resource.post(apiPrefix + "/v1/entities/su-clanovinapomene/add", { data: data.data });
},



delete(data) {
  const paramData = {
    slug: "su-clanovinapomene",
    data: [{ field: 'id', value: data.id }],
  };
  return resource.delete(apiPrefix + "/v1/entities/su-clanovinapomene/delete", { data: paramData });
},


};
