import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";



export default {


 citanjeispiti( params ) {
  
    const ep = apiPrefix + "/v1/entities/su-ispiti/citanjeispiti";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },
  

read(data) {
  const ep = apiPrefix + "/v1/entities/su-ispiti/read";
  const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-ispiti";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  all(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-ispiti" + "/all";
    const url = ep;
    return resource.get(url);
  },


  readBySlug(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-ispiti/read?slug=su-ispiti&idedukacijskogsegmentaclana={idmember}";
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

    return resource.put(apiPrefix + "/v1/entities/su-ispiti/edit", { data: data });

  //  return resource.put(apiPrefix + "/v1/entities/su-ispiti/edit", data);
  },

  update(data = []) {
  const ep = apiPrefix + "/v1/entities/su-ispiti/update";
  return resource.put(ep, data);
},


add(data) {

 
  for (const key in data.data) {
    // Check if the property has value null or an empty string
    if (data.data[key] === null || data.data[key] === '') {
        // If it does, delete that property from data.data
        delete data.data[key];
    }
}

  return resource.post(apiPrefix + "/v1/entities/su-ispiti/add", { data: data.data });
},



delete(data) {
  const paramData = {
    slug: "su-ispiti",
    data: [{ field: 'id', value: data.id }],
  };
  return resource.delete(apiPrefix + "/v1/entities/su-ispiti/delete", { data: paramData });
},


};
