import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";



export default {


 citanje(params) {
  
    const ep = apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },
  

read(data) {
  const ep = apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci/read";
  const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },
  

  all(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci" + "/all";
    const url = ep;
    return resource.get(url);
  },


  readBySlug(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci/read?slug=su-clanoviedukacijskipodaci&idmember={idmember}";
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


    return resource.put(apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci/edit", { data: data });

  //  return resource.put(apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci/edit", data);
  },


  update(data = []) {
  const ep = apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci/update";
  return resource.put(ep, data);
},




add(data) {

  console.log('DATUM TEST PROBLEM data API: ', data);
  console.log('DATUM TEST PROBLEM zavrsenaedukacija API: ', data.data.zavrsenaedukacija);


  for (const key in data.data) {
    // Check if the property has value null or an empty string
    if (data.data[key] === null || data.data[key] === '') {
        // If it does, delete that property from data.data
        delete data.data[key];
    }
}



  return resource.post(apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci/add", { data: data.data });
},

sort(data) {
  return resource.put(entityPrefix + "/" + data.slug + "/sort", data);
},



delete(data) {
  const paramData = {
    slug: "su-clanoviedukacijskipodaci",
    data: [{ field: 'id', value: data.id }],
  };
  return resource.delete(apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci/delete", { data: paramData });
},


zadnjimaticni() {
  const ep = apiPrefix + "/v1/entities/su-clanoviedukacijskipodaci" + "/zadnjimaticni";
  const url = ep;
  return resource.get(url);
},


};
