import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";



export default {


 citanje(params ) {
  
    const ep = apiPrefix + "/v1/entities/su-edukacijskagrupasegmentclan/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },
  

read(data) {
  const ep = apiPrefix + "/v1/entities/su-edukacijskagrupasegmentclan/read";
  const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-edukacijskagrupasegmentclan";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  all(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-edukacijskagrupasegmentclan" + "/all";
    const url = ep;
    return resource.get(url);
  },


  readBySlug(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-edukacijskagrupasegmentclan/read?slug=su-edukacijskagrupasegmentclan&idmember={idmember}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    for (const key in data) {
      // Check if the property has value null or an empty string
      if (data[key] === null || data[key] === '') {
          // If it does, delete that property from data.data
          delete data[key];
      }
  }
    return resource.put(apiPrefix + "/v1/entities/su-edukacijskagrupasegmentclan/edit", { data: data });

  //  return resource.put(apiPrefix + "/v1/entities/su-edukacijskagrupasegmentclan/edit", data);
  },

  update(data = []) {
  const ep = apiPrefix + "/v1/entities/su-edukacijskagrupasegmentclan/update";
  return resource.put(ep, data);
},


add(data) {

  if (!data.paydate || data.paydate.trim() === '') {
    data.paydate = null;
  }
  if (!data.opendate || data.opendate.trim() === '') {
    data.opendate = null;
  }
  return resource.post(apiPrefix + "/v1/entities/su-edukacijskagrupasegmentclan/add", { data: data.data });
},



delete(data) {
  const paramData = {
    slug: "su-edukacijskagrupasegmentclan",
    data: [{ field: 'id', value: data.id }],
  };
  return resource.delete(apiPrefix + "/v1/entities/su-edukacijskagrupasegmentclan/delete", { data: paramData });
},


};
