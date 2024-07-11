import resource from "../resource";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {
  saveTokenMessage(firebaseTokenMessages) {
    const url = `${apiPrefix}/v1/firebase/cloud_messages/save-token-messages`;
    return resource.put(url, {
      token_get_message: firebaseTokenMessages,
    });
  },
  getMessages() {
    const url = `${apiPrefix}/v1/firebase/messages`;
    return resource.get(url);
  },
  readMessage(id) {
    const url = `${apiPrefix}/v1/firebase/messages/${id}`;
    return resource.put(url);
  },
  getCountUnreadMessage() {
    const url = `${apiPrefix}/v1/firebase/messages/count-unread`;
    return resource.get(url);
  },


  sendFirebaseMessage() {
    const url = `${apiPrefix}/v1/firebase/cloud_messages/send-firebase-message`;
    return resource.post(url);
  },

  clearAllNotifications() {
    const url = `${apiPrefix}/v1/firebase/messages/clear-all`;
    return resource.delete(url);
  },
  
};
