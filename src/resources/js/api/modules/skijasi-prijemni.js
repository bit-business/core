import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/su-prijemni";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  citanje(params = {}) {

    const ep = apiPrefix + "/v1/entities/su-prijemni/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/entities/su-prijemni/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  readBySlug(data) {
    const ep = apiPrefix + "/v1/entities/su-prijemni/read?slug=su-prijemni&id={id}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/su-prijemni/edit", data);
  },

  add(data) {
    if (!data.filedate || data.filedate.trim() === '') {
      data.filedate = null;
    }
    return resource.post(apiPrefix + "/v1/entities/su-prijemni/add", data);
  },

  delete(data) {
    const paramData = {
      slug: "su-prijemni",
      data: [{ field: 'id', value: data.id }],
    };
    return resource.delete(apiPrefix + "/v1/entities/su-prijemni/delete", { data: paramData });
  },
  
  
  };
  