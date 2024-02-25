#define FFI_LIB "libnotify.so.4"

typedef signed short gint16;
typedef int gint;
typedef bool gboolean;
typedef void* gpointer;
typedef unsigned int gsize;
typedef unsigned int guint;
typedef unsigned long gulong;
typedef gulong GType;

typedef struct _GTypeClass GTypeClass;
typedef struct _GSList GSList;
typedef struct _GTypeInstance GTypeInstance;
typedef struct _GObject GObject;
typedef struct _GObjectClass GObjectClass;
typedef struct _GObject GInitiallyUnowned;
typedef struct _GObjectClass GInitiallyUnownedClass;
typedef struct _GObjectConstructParam GObjectConstructParam;

typedef struct _NotifyNotification NotifyNotification;
typedef struct _NotifyNotificationClass NotifyNotificationClass;
typedef struct _NotifyNotificationPrivate NotifyNotificationPrivate;

typedef struct _GTypeInstanceError GError;

struct _GSList
{
  gpointer data;
  GSList *next;
};

struct _GTypeClass {
    GType g_type;
};

struct  _GObjectClass
{
  GTypeClass   g_type_class;
  GSList      *construct_properties;
  gsize   flags;
  gpointer  pdummy[6];
};

struct _GTypeInstance {
    GTypeClass *g_class;
};

struct  _GObject
{
  GTypeInstance  g_type_instance;
  guint ref_count;
  // GData *qdata;
};

struct _NotifyNotification
{
        /*< private >*/
        GObject                    parent_object;

        NotifyNotificationPrivate *priv;
};

struct _NotifyNotificationClass
{
        GObjectClass    parent_class;

        /* Signals */
        void            (*closed) (NotifyNotification *notification);
};


gboolean notify_init(const char *app_name);
gboolean notify_is_initted (void);
void notify_uninit (void);
const char* notify_get_app_name ( void );
NotifyNotification *notify_notification_new(const char *summary, const char *body, const char *icon);
gboolean notify_notification_show (NotifyNotification *notification, GError **error);
void g_object_unref (gpointer object);
