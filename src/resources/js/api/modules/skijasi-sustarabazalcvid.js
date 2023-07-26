import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";



export default {


 citanje(params ) {
  
    const ep = apiPrefix + "/v1/entities/su-starabazapoveznice/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },
  

read(data) {
  const ep = apiPrefix + "/v1/entities/su-starabazapoveznice/read";
  const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-starabazapoveznice";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  all(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-starabazapoveznice" + "/all";
    const url = ep;
    return resource.get(url);
  },


  readBySlug(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-starabazapoveznice/read?slug=su-starabazapoveznice&idmember={idmember}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/su-starabazapoveznice/edit", { data: data });

  //  return resource.put(apiPrefix + "/v1/entities/su-starabazapoveznice/edit", data);
  },

  update(data = []) {
  const ep = apiPrefix + "/v1/entities/su-starabazapoveznice/update";
  return resource.put(ep, data);
},


add(data) {

  if (!data.paydate || data.paydate.trim() === '') {
    data.paydate = null;
  }
  if (!data.opendate || data.opendate.trim() === '') {
    data.opendate = null;
  }
  return resource.post(apiPrefix + "/v1/entities/su-starabazapoveznice/add", { data: data.data });
},



delete(data) {
  const paramData = {
    slug: "su-starabazapoveznice",
    data: [{ field: 'id', value: data.id }],
  };
  return resource.delete(apiPrefix + "/v1/entities/su-starabazapoveznice/delete", { data: paramData });
},


};
