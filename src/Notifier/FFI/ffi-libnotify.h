#define FFI_LIB "libnotify.so.4"

typedef bool gboolean;
typedef void* gpointer;
typedef struct _NotifyNotification NotifyNotification;
typedef struct _GTypeInstanceError GError;

gboolean notify_init(const char *app_name);
gboolean notify_is_initted (void);
void notify_uninit (void);
NotifyNotification *notify_notification_new(const char *summary, const char *body, const char *icon);
gboolean notify_notification_show (NotifyNotification *notification, GError **error);
void g_object_unref (gpointer object);
