import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  browse(data = {}) {
    const ep = apiPrefix + "/v1/activitylogs";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  read(data) {
    const ep = apiPrefix + "/v1/activitylogs/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },
  getstats(data = {}) {
    const ep = apiPrefix + "/v1/activitylogs/stats";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },
};
