import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/entities/tbl-members";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  all(data = {}) {
    const ep = apiPrefix + "/v1/entities/hzuts-clanovi" + "/all";
    const url = ep;
    return resource.get(url);
  },


  citanje(params = {}) {

    const ep = apiPrefix + "/v1/entities/hzuts-clanovi/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/entities/hzuts-clanovi/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },
  read2(data) {
    const ep = apiPrefix + "/v1/entities/hzuts-clanovi/read2";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  readBySlug(data) {
    const ep = apiPrefix + "/v1/entities/hzuts-clanovi/read?slug=hzuts-clanovis&id={id}";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    return resource.put(apiPrefix + "/v1/entities/hzuts-clanovi/edit", { data: data });
  },

  add(data) {
    return resource.post(apiPrefix + "/v1/entities/hzuts-clanovi/add", data);
  },

  delete(data) {
    const paramData = {
      slug: "hzuts-clanovi",
      data: [{ field: 'id', value: data.id }],
    };
    return resource.delete(apiPrefix + "/v1/entities/hzuts-clanovi/delete", { data: paramData });
  },
  
  
  };
  