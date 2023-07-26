import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";



export default {


 citanje(params) {
  
    const ep = apiPrefix + "/v1/entities/su-edukacijskiprogrami/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },
  

read(data) {
  const ep = apiPrefix + "/v1/entities/su-edukacijskiprogrami/read";
  const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-edukacijskiprogrami";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  all(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-edukacijskiprogrami" + "/all";
    const url = ep;
    return resource.get(url);
  },


  readBySlug(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-edukacijskiprogrami/read?slug=su-edukacijskiprogrami&idmember={idmember}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/su-edukacijskiprogrami/edit", { data: data });

  //  return resource.put(apiPrefix + "/v1/entities/su-edukacijskiprogrami/edit", data);
  },

  update(data = []) {
  const ep = apiPrefix + "/v1/entities/su-edukacijskiprogrami/update";
  return resource.put(ep, data);
},


add(data) {

  if (!data.paydate || data.paydate.trim() === '') {
    data.paydate = null;
  }
  if (!data.opendate || data.opendate.trim() === '') {
    data.opendate = null;
  }
  return resource.post(apiPrefix + "/v1/entities/su-edukacijskiprogrami/add", { data: data.data });
},



delete(data) {
  const paramData = {
    slug: "su-edukacijskiprogrami",
    data: [{ field: 'id', value: data.id }],
  };
  return resource.delete(apiPrefix + "/v1/entities/su-edukacijskiprogrami/delete", { data: paramData });
},


};
