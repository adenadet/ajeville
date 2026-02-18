const ChatDashboard                        = () => import('../../chat/Dashboard.vue');
const ChatCompose                          = () => import('../../chat/Compose.vue');
const ChatInbox                            = () => import('../../chat/Inbox.vue');
const ChatMessage                          = () => import('../../chat/Message.vue');
const ChatMessages                         = () => import('../../chat/Messages.vue');
const ChatOutbox                           = () => import('../../chat/Outbox.vue');
const ChatRooms                            = () => import('../../chat/Rooms.vue');
const ChatRoom                             = () => import('../../chat/Room.vue');

    const ChatDetailMessageList                       = () => import('../../chat/details/MessageList.vue');
    const ChatDetailMessageView                       = () => import('../../chat/details/MessageView.vue');
    const ChatDetailRoomList                          = () => import('../../chat/details/RoomList.vue');

    const ChatFormMessage                             = () => import('../../chat/forms/Message.vue');

export default[
    {path: '/chats/dashboard',                                  component: ChatDashboard},
    {path: '/chats/rooms',                                      component: ChatRooms},
    {path: '/chats/rooms/:id',                                  component: ChatRoom},
];