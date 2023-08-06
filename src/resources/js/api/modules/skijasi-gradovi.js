import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-cities";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  all(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-cities" + "/all";
    const url = ep;
    return resource.get(url);
  },


  citanje(params = {}) {

    const ep = apiPrefix + "/v1/entities/tbl-cities/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/entities/tbl-cities/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },
  read2(data) {
    const ep = apiPrefix + "/v1/entities/tbl-cities/read2";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  readBySlug(data) {
    const ep = apiPrefix + "/v1/entities/tbl-cities/read?slug=tbl-cities&id={id}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/tbl-cities/edit", { data: data });
  },

  add(data) {
    return resource.post(apiPrefix + "/v1/entities/tbl-cities/add", data);
  },

  delete(data) {
    const paramData = {
      slug: "tbl-cities",
      data: [{ field: 'id', value: data.id }],
    };
    return resource.delete(apiPrefix + "/v1/entities/tbl-cities/delete", { data: paramData });
  },
  
  
  };
  