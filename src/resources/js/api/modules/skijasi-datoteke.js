import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-datoteke";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  citanje(params = {}) {

    const ep = apiPrefix + "/v1/entities/tbl-datoteke/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/entities/tbl-datoteke/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  readBySlug(data) {
    const ep = apiPrefix + "/v1/entities/tbl-datoteke/read?slug=tbl-datoteke&id={id}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },


  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/tbl-datoteke/edit", data);
  },

  add(data) {
    if (!data.filedate || data.filedate.trim() === '') {
      data.filedate = null;
    }
    return resource.post(apiPrefix + "/v1/entities/tbl-datoteke/add", data);
  },

  delete(data) {
    const paramData = {
      slug: "tbl-datoteke",
      data: [{ field: 'id', value: data.id }],
    };
    return resource.delete(apiPrefix + "/v1/entities/tbl-datoteke/delete", { data: paramData });
  },
  
  
  };
  