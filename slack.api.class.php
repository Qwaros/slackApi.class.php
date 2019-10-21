<?php

/**
 * Class slackApi
 */
class slackApi
{
    /**
     * @var
     */
    protected $token;
    /**
     * @var
     */
    protected $request_status;
    /**
     * @var
     */
    protected $request_response;
    /**
     * @var
     */
    protected $error;

    /**
     * @param string $method
     * @param array $data
     * @return bool|array
     */
    private function sendPostRequest($method, $data = array())
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://slack.com/api/" . $method);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json; charset=utf-8',
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:54.0) Gecko/20100101 Firefox/54.0',
            'Authorization: Bearer ' . $this->token
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $this->request_response = curl_exec($ch);
        $this->request_status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        curl_close($ch);

        return $this->request_status == 200 ? json_decode($this->request_response) : false;
    }

    /**
     * @param string $method
     * @param array $data
     * @return bool|array
     */
    private function sendGetRequest($method, $data = array())
    {
        $ch = curl_init("https://slack.com/api/{$method}?" . http_build_query($data, '', '&'));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $this->request_response = curl_exec($ch);
        $this->request_status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        curl_close($ch);

        return $this->request_status == 200 ? json_decode($this->request_response) : false;
    }

    /**
     * slackApi constructor.
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @param string $app_id
     * @param string $request_id
     * @param string $team_id
     * @return bool
     */
    public function adminAppsApprove($app_id, $request_id = "", $team_id = "")
    {
        self::sendPostRequest("admin.apps.approve", array("token" => $this->token, "app_id" => $app_id, "request_id" => $request_id, "team_id" => $team_id));
        $json = json_decode($this->request_response);
        if ($json->ok === true) {
            return true;
        } else {
            $this->error = $json->error;
            return false;
        }
    }

    /**
     * @param string $app_id
     * @param string $request_id
     * @param string $team_id
     * @return bool
     */
    public function adminAppsRestrict($app_id, $request_id = "", $team_id = "")
    {
        self::sendPostRequest("admin.apps.restrict", array("token" => $this->token, "app_id" => $app_id, "request_id" => $request_id, "team_id" => $team_id));
        $json = json_decode($this->request_response);
        if ($json->ok === true) {
            return true;
        } else {
            $this->error = $json->error;
            return false;
        }
    }

    /**
     * @param int $limit
     * @param string $cursor
     * @param string $team_id
     * @return bool|array
     */
    public function adminAppsRequestsList($limit = 100, $cursor = "", $team_id = "")
    {
        return self::sendGetRequest("admin.apps.requests.list", array("token" => $this->token, "limit" => $limit, "cursor" => $cursor, "team_id" => $team_id));
    }

    /**
     * @param string $user_id
     * @param bool $mobile_only
     * @param bool $web_only
     * @return bool|array
     */
    public function adminUsersSessionReset($user_id, $mobile_only = false, $web_only = false)
    {
        return self::sendPostRequest("admin.users.session.reset", array("token" => $this->token, "user_id" => $user_id, "mobile_only" => $mobile_only, "web_only" => $web_only));
    }

    /**
     * @param string $error
     * @param string $external_param
     * @return bool|array
     */
    public function apiTest($error = "", $external_param = "")
    {
        return self::sendPostRequest("api.test", array("token" => $this->token, "error" => $error, "foo" => $external_param));
    }

    /**
     * @return bool|array
     */
    public function appsPermissionsInfo()
    {
        return self::sendGetRequest("apps.permissions.info", array("token" => $this->token));
    }

    /**
     * @param string $scopes
     * @param string $trigger_id
     * @return bool|array
     */
    public function appsPermissionsRequest($scopes, $trigger_id = "")
    {
        return self::sendGetRequest("apps.permissions.request", array("token" => $this->token, "scopes" => $scopes, "trigger_id" => $trigger_id));
    }

    /**
     * @param string $cursor
     * @param int $limit
     * @return bool|array
     */
    public function appsPermissionsResourcesList($cursor, $limit = 20)
    {
        return self::sendGetRequest("apps.permissions.resources.list", array("token" => $this->token, "cursor" => $cursor, "limit" => $limit));
    }

    /**
     * @return bool|array
     */
    public function appsPermissionsScopesList()
    {
        return self::sendGetRequest("apps.permissions.scopes.list", array("token" => $this->token));
    }

    /**
     * @param string $cursor
     * @param int $limit
     * @return bool|array
     */
    public function appsPermissionsUsersList($cursor = "", $limit = 20)
    {
        return self::sendGetRequest("apps.permissions.resources.list", array("token" => $this->token, "cursor" => $cursor, "limit" => $limit));
    }

    /**
     * @param string $user
     * @param string|array $scopes
     * @param string $trigger_id
     * @return bool|array
     */
    public function appsPermissionsUsersRequest($user, $scopes, $trigger_id)
    {
        $scopes = is_array($scopes) ? implode(",", $scopes) : $scopes;
        return self::sendGetRequest("apps.permissions.users.request", array("token" => $this->token, "user" => $user, "scopes" => $scopes, "trigger_id" => $trigger_id));
    }

    /**
     * @param string $client_id
     * @param string $client_secret
     * @return bool|array
     */
    public function appsUninstall($client_id, $client_secret)
    {
        return self::sendGetRequest("apps.uninstall", array("token" => $this->token, "client_id" => $client_id, "client_secret" => $client_secret));
    }

    /**
     * @param bool $test
     * @return bool|array
     */
    public function authRevoke($test = false)
    {
        return self::sendGetRequest("auth.revoke", array("token" => $this->token, "test" => $test));
    }

    /**
     * @return bool|array
     */
    public function authTest()
    {
        return self::sendGetRequest("auth.test", array("token" => $this->token));
    }

    /**
     * @param string $bot
     * @return bool|array
     */
    public function botsInfo($bot = "")
    {
        return self::sendGetRequest("bots.info", array("token" => $this->token, "bot" => $bot));
    }

    /*Channels methods*/
    /**
     * @param string $channel
     * @return bool|array
     */
    public function channelsArchive($channel)
    {
        return self::sendPostRequest("channels.archive", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $name
     * @param bool $validate
     * @return bool|array
     */
    public function channelsCreate($name, $validate = true)
    {
        return self::sendPostRequest("channels.create", array("token" => $this->token, "name" => $name, "validate" => $validate));
    }

    /**
     * @param string $channel
     * @param string $latest
     * @param string $oldest
     * @param bool $inclusive
     * @param int $count
     * @param bool $unreads
     * @return bool|array
     */
    public function channelsHistory($channel, $latest = "", $oldest = "", $inclusive = true, $count = 100, $unreads = false)
    {
        return self::sendGetRequest("channels.history", array("token" => $this->token, "channel" => $channel, "latest" => $latest, "oldest" => $oldest, "inclusive" => $inclusive, "count" => $count, "unreads" => $unreads));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function channelsInfo($channel)
    {
        return self::sendGetRequest("channels.info", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $channel
     * @param $user
     * @return bool|array
     */
    public function channelsInvite($channel, $user)
    {
        return self::sendPostRequest("channels.invite", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    /**
     * @param string $name
     * @param bool $validate
     * @return bool|array
     */
    public function channelsJoin($name, $validate = true)
    {
        return self::sendPostRequest("channels.join", array("token" => $this->token, "name" => $name, "validate" => $validate));
    }

    /**
     * @param string $channel
     * @param string $user
     * @return bool|array
     */
    public function channelsKick($channel, $user)
    {
        return self::sendPostRequest("channels.kick", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function channelsLeave($channel)
    {
        return self::sendPostRequest("channels.leave", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $channel
     * @param float $ts
     * @return bool|array
     */
    public function channelsMark($channel, $ts)
    {
        return self::sendPostRequest("channels.mark", array("token" => $this->token, "channel" => $channel, "ts" => $ts));
    }

    /**
     * @param string $channel
     * @param string $name
     * @param bool $validate
     * @return bool|array
     */
    public function channelsRename($channel, $name, $validate = true)
    {
        return self::sendPostRequest("channels.rename", array("token" => $this->token, "channel" => $channel, "name" => $name, "validate" => $validate));
    }

    /**
     * @param string $channel
     * @param float $thread_ts
     * @return bool|array
     */
    public function channelsReplies($channel, $thread_ts)
    {
        return self::sendGetRequest("channels.replies", array("token" => $this->token, "channel" => $channel, "thread_ts" => $thread_ts));
    }

    /**
     * @param string $channel
     * @param string $purpose
     * @param bool $name_tagging
     * @return bool|array
     */
    public function channelsSetPurpose($channel, $purpose, $name_tagging = true)
    {
        return self::sendPostRequest("channels.setPurpose", array("token" => $this->token, "channel" => $channel, "purpose" => $purpose, "name_tagging" => $name_tagging));
    }

    /**
     * @param string $channel
     * @param string $topic
     * @return bool|array
     */
    public function channelsSetTopic($channel, $topic)
    {
        return self::sendPostRequest("channels.setTopic", array("token" => $this->token, "channel" => $channel, "topic" => $topic));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function channelsUnarchive($channel)
    {
        return self::sendPostRequest("channels.unarchive", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $cursor
     * @return bool|array
     */
    public function channelsList($cursor = "")
    {
        return self::sendGetRequest("channels.list", array("token" => $this->token, "cursor" => $cursor));
    }

    /**
     * @param string $channel
     * @param float $ts
     * @param bool $as_user
     * @return bool|array
     */
    public function chatDelete($channel, $ts, $as_user = false)
    {
        return self::sendPostRequest("chat.delete", array("token" => $this->token, "channel" => $channel, "ts" => $ts, "as_user" => $as_user));
    }

    /**
     * @param string $channel
     * @param string $scheduled_message_id
     * @param bool $as_user
     * @return bool|array
     */
    public function chatDeleteScheduledMessage($channel, $scheduled_message_id, $as_user = false)
    {
        return self::sendPostRequest("chat.deleteScheduledMessage", array("token" => $this->token, "channel" => $channel, "scheduled_message_id" => $scheduled_message_id, "as_user" => $as_user));
    }

    /**
     * @param string $channel
     * @param float $message_ts
     * @return bool|array
     */
    public function chatGetPermalink($channel, $message_ts)
    {
        return self::sendPostRequest("chat.getPermalink", array("token" => $this->token, "channel" => $channel, "message_ts" => $message_ts));
    }

    /*Chats methods*/
    /**
     * @param string $channel
     * @param string $message
     * @return bool|array
     */
    public function chatMeMessage($channel, $message)
    {
        return self::sendPostRequest("chat.meMessage", array("token" => $this->token, "channel" => $channel, "message_ts" => $message));
    }

    /**
     * @param string $channel
     * @param string $text
     * @param array $attachments
     * @param string $user
     * @param bool $as_user
     * @return bool|array
     */
    public function chatPostEphemeral($channel, $text, $attachments, $user, $as_user)
    {
        return self::sendPostRequest("chat.postEphemeral", array("token" => $this->token, "channel" => $channel, "text" => $text, "attachments" => $attachments, "user" => $user, "as_user" => $as_user));
    }

    /**
     * @param string $channel
     * @param bool $as_user
     * @param string $thread_ts
     * @param string $text
     * @param array $attachments
     * @param array $blocks
     * @param string $icon_emoji
     * @param string $icon_url
     * @param bool $link_names
     * @param bool $mrkdwn
     * @param string $parse
     * @param bool $reply_broadcast
     * @param bool $unfurl_links
     * @param bool $unfurl_media
     * @param string $username
     * @return bool|array
     */
    public function chatPostMessage($channel, $as_user = false, $thread_ts = "", $text = "", $attachments = array(), $blocks = array(), $icon_emoji = "", $icon_url = "", $link_names = true, $mrkdwn = false, $parse = "full", $reply_broadcast = true, $unfurl_links = true, $unfurl_media = false, $username = "")
    {
        return self::sendPostRequest("chat.postMessage", array("token" => $this->token, "channel" => $channel, "text" => $text, "attachments" => $attachments, "blocks" => $blocks, "icon_emoji" => $icon_emoji, "as_user" => $as_user, "icon_url" => $icon_url, "link_names" => $link_names, "mrkdwn" => $mrkdwn, "parse" => $parse, "reply_broadcast" => $reply_broadcast, "thread_ts" => $thread_ts, "unfurl_links" => $unfurl_links, "unfurl_media" => $unfurl_media, "username" => $username));
    }

    /**
     * @param string $channel
     * @param float $post_at
     * @param string $text
     * @param array $attachments
     * @param array $blocks
     * @param bool $as_user
     * @param string $thread_ts
     * @param bool $link_names
     * @param string $parse
     * @param bool $reply_broadcast
     * @param bool $unfurl_links
     * @param bool $unfurl_media
     * @return bool|array
     */
    public function chatScheduleMessage($channel, $post_at, $text = "", $attachments = array(), $blocks = array(), $as_user = false, $thread_ts = "", $link_names = true, $parse = "full", $reply_broadcast = true, $unfurl_links = true, $unfurl_media = false)
    {
        return self::sendPostRequest("chat.scheduleMessage", array("token" => $this->token, "channel" => $channel, "text" => $text, "attachments" => $attachments, "blocks" => $blocks, "link_names" => $link_names, "parse" => $parse, "reply_broadcast" => $reply_broadcast, "thread_ts" => $thread_ts, "unfurl_links" => $unfurl_links, "unfurl_media" => $unfurl_media, "as_user" => $as_user, "post_at" => $post_at));
    }

    /**
     * @param string $channel
     * @param float $ts
     * @param string $unfurls (URL-encoded JSON map with keys set to URLs featured in the the message, pointing to their unfurl blocks or message attachments.)
     * @param string $user_auth_message
     * @param bool $user_auth_required
     * @param string $user_auth_url
     * @return bool|array
     */
    public function chatUnfurl($channel, $ts, $unfurls, $user_auth_message = "", $user_auth_required = false, $user_auth_url = "")
    {
        return self::sendPostRequest("chat.unfurl", array("token" => $this->token, "channel" => $channel, "ts" => $ts, "unfurls" => $unfurls, "user_auth_message" => $user_auth_message, "user_auth_required" => $user_auth_required, "user_auth_url" => $user_auth_url));
    }

    /**
     * @param string $channel
     * @param string $text
     * @param float $ts
     * @param bool $as_user
     * @param array $attachments
     * @param array $blocks
     * @param bool $link_names
     * @param string $parse
     * @return bool|array
     */
    public function chatUpdate($channel, $text, $ts, $as_user = false, $attachments = array(), $blocks = array(), $link_names = true, $parse = "none")
    {
        return self::sendPostRequest("chat.update", array("token" => $this->token, "channel" => $channel, "text" => $text, "ts" => $ts, "as_user" => $as_user, "attachments" => $attachments, "blocks" => $blocks, "link_names" => $link_names, "parse" => $parse));
    }

    /**
     * @param string $channel
     * @param string $latest
     * @param string $oldest
     * @param int $limit
     * @param string $cursor
     * @return bool|array
     */
    public function chatScheduledMessagesList($channel, $latest = "", $oldest = "", $limit = 100, $cursor = "")
    {
        return self::sendPostRequest("chat.scheduledMessages.list", array("token" => $this->token, "channel" => $channel, "latest" => $latest, "oldest" => $oldest, "limit" => $limit, "cursor" => $cursor));
    }

    /*Conversations methods*/
    /**
     * @param string $channel
     * @return bool|array
     */
    public function conversationsArchive($channel)
    {
        return self::sendPostRequest("conversations.archive", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function conversationsClose($channel)
    {
        return self::sendPostRequest("conversations.close", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $name
     * @param bool $is_private
     * @return bool|array
     */
    public function conversationsCreate($name, $is_private = false, $is_private = false)
    {
        return self::sendPostRequest("conversations.create", array("token" => $this->token, "name" => $name, "is_private" => $is_private));
    }

    /**
     * @param string $channel
     * @param string $cursor
     * @param bool $inclusive
     * @param float $latest
     * @param float $oldest
     * @param int $limit
     * @return bool|array
     */
    public function conversationsHistory($channel, $cursor = "", $inclusive = true, $latest = 0.0, $oldest = 0.0, $limit = 20)
    {
        $latest = $latest = 0.0 ? "now" : $latest;
        $oldest = $oldest = 0.0 ? 0 : $oldest;
        return self::sendGetRequest("conversations.history", array("token" => $this->token, "channel" => $channel, "cursor" => $cursor, "inclusive" => $inclusive, "latest" => $latest, "oldest" => $oldest, "limit" => $limit));
    }

    /**
     * @param string $channel
     * @param bool $include_locale
     * @param bool $include_num_members
     * @return bool|array
     */
    public function conversationsInfo($channel, $include_locale = true, $include_num_members = true)
    {
        return self::sendGetRequest("conversationsI.info", array("token" => $this->token, "channel" => $channel, "include_locale" => $include_locale, "include_num_members" => $include_num_members));
    }

    /**
     * @param string $channel
     * @param string $user
     * @return bool|array
     */
    public function conversationsInvite($channel, $user)
    {
        return self::sendPostRequest("conversations.invite", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function conversationsJoin($channel)
    {
        return self::sendPostRequest("conversations.join", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $channel
     * @param string $user
     * @return bool|array
     */
    public function conversationsKick($channel, $user)
    {
        return self::sendPostRequest("conversations.kick", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    /**
     * @param string $cursor
     * @param bool $exclude_archived
     * @param int $limit
     * @param string $types (public_channel, private_channel, mpim, im)
     * @return bool|array
     */
    public function conversationsList($cursor = "", $exclude_archived = true, $limit = 20, $types = "")
    {
        return self::sendGetRequest("conversations.list", array("token" => $this->token, "cursor" => $cursor, "exclude_archived" => $exclude_archived, "limit" => $limit, "types" => $types));
    }

    /**
     * @param string $channel
     * @param int $limit
     * @param string $cursor
     * @return bool|array
     */
    public function conversationsMembers($channel, $limit = 20, $cursor = "")
    {
        return self::sendGetRequest("conversations.members", array("token" => $this->token, "channel" => $channel, "limit" => $limit, "cursor" => $cursor));
    }

    /**
     * @param string $channel
     * @param string $return_im
     * @param string $users
     * @return bool|array
     */
    public function conversationsOpen($channel = "", $return_im = "", $users = "")
    {
        return self::sendPostRequest("conversations.open", array("token" => $this->token, "channel" => $channel, "return_im" => $return_im, "users" => $users));
    }

    /**
     * @param string $channel
     * @param string $name
     * @return bool|array
     */
    public function conversationsRename($channel, $name)
    {
        return self::sendPostRequest("conversations.rename", array("token" => $this->token, "channel" => $channel, "name" => $name));
    }

    /**
     * @param string $channel
     * @param float $ts
     * @param string $cursor
     * @param bool $inclusive
     * @param string $latest
     * @param string $oldest
     * @param int $limit
     * @return bool|array
     */
    public function conversationsReplies($channel, $ts, $cursor = "", $inclusive = true, $latest = "", $oldest = "", $limit = 20)
    {
        return self::sendGetRequest("conversations.replies", array("token" => $this->token, "channel" => $channel, "ts" => $ts, "cursor" => $cursor, "inclusive" => $inclusive, "latest" => $latest, "oldest" => $oldest, "limit" => $limit));
    }

    /**
     * @param string $channel
     * @param string $purpose
     * @return bool|array
     */
    public function conversationsSetPurpose($channel, $purpose)
    {
        return self::sendPostRequest("conversations.setPurpose", array("token" => $this->token, "channel" => $$channel, "purpose" => $purpose));
    }

    /**
     * @param string $channel
     * @param string $topic
     * @return bool|array
     */
    public function conversationsSetTopic($channel, $topic)
    {
        return self::sendPostRequest("conversations.setTopic", array("token" => $this->token, "channel" => $channel, "topic" => $topic));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function conversationsUnarchive($channel)
    {
        return self::sendPostRequest("conversations.unarchive", array("token" => $this->token, "channel" => $channel));
    }

    /*Dialogs methods*/
    /**
     * @param string $dialog
     * @param string $trigger_id
     * @return bool|array
     */
    public function dialogOpen($dialog, $trigger_id)
    {
        return self::sendPostRequest("dialog.open", array("token" => $this->token, "dialog" => $dialog, "trigger_id" => $trigger_id));
    }

    /**
     * @return bool|array
     */
    public function dndEndDnd()
    {
        return self::sendPostRequest("dnd.endDnd", array("token" => $this->token));
    }

    /**
     * @return bool|array
     */
    public function dndEndSnooze()
    {
        return self::sendPostRequest("dnd.endSnooze", array("token" => $this->token));
    }

    /**
     * @param string $user
     * @return bool|array
     */
    public function dndInfo($user = "")
    {
        return self::sendPostRequest("dnd.info", array("token" => $this->token, "user" => $user));
    }

    /**
     * @param int $num_minutes
     * @return bool|array
     */
    public function dndSetSnooze($num_minutes = 60)
    {
        return self::sendPostRequest("dnd.setSnooze", array("token" => $this->token, "num_minutes" => $num_minutes));
    }

    /**
     * @param string $users
     * @return bool|array
     */
    public function dndTeamInfo($users = "")
    {
        return self::sendGetRequest("dnd.teamInfo", array("token" => $this->token, "users" => $users));
    }

    /**
     * @return bool|array
     */
    public function emojiList()
    {
        return self::sendGetRequest("emoji.list", array("token" => $this->token));
    }

    /**
     * @param string $file
     * @param string $id
     * @return bool|array
     */
    public function filesCommentsDelete($file, $id)
    {
        return self::sendPostRequest("files.commentsDelete", array("token" => $this->token, "file" => $file, "id" => $id));
    }

    /*Files*/
    /**
     * @param string $file
     * @return bool|array
     */
    public function filesDelete($file)
    {
        return self::sendPostRequest("files.delete", array("token" => $this->token, "file" => $file));
    }

    /**
     * @param string $file
     * @param int $count
     * @param string $cursor
     * @return bool|array
     */
    public function filesInfo($file, $count = 100, $cursor = "")
    {
        return self::sendGetRequest("files.info", array("token" => $this->token, "file" => $file, "count" => $count, "cursor" => $cursor));
    }

    /**
     * @param string $channel
     * @param int $ts_from
     * @param string $ts_to
     * @param int $count
     * @param int $page
     * @param string $types
     * @param string $user
     * @return bool|array
     */
    public function filesList($channel = "", $ts_from = 0, $ts_to = "now", $count = 20, $page = 1, $types = "all", $user = "")
    {
        return self::sendGetRequest("files.list", array("token" => $this->token, "channel" => $channel, "count" => $count, "page" => $page, "ts_from" => $ts_from, "ts_to" => $ts_to, "types" => $types, "user" => $user));
    }

    /**
     * @param string $file
     * @return bool|array
     */
    public function filesRevokePublicURL($file)
    {
        return self::sendPostRequest("files.revokePublicURL", array("token" => $this->token, "file" => $file));
    }

    /**
     * @param string $file
     * @return bool|array
     */
    public function filesSharedPublicURL($file)
    {
        return self::sendPostRequest("files.sharedPublicURL", array("token" => $this->token, "file" => $file));
    }

    /**
     * @param bool $json
     * @param string $channels
     * @param string $initial_comment
     * @param string $title
     * @param string $content
     * @param string $file
     * @param string $thread_ts
     * @param string $filename
     * @param string $filetype
     * @return bool|array
     */
    public function filesUpload($json = true, $channels = "", $initial_comment = "", $title = "", $content = "", $file = "", $thread_ts = "", $filename = "", $filetype = "")
    {
        //TODO Расширить под multipart
        if ($json) {
            return self::sendPostRequest("files.upload", array("token" => $this->token, "channels" => $channels, "initial_comment" => $initial_comment, "title" => $title, "content" => $content, "thread_ts" => $thread_ts, "filename" => $filename, "filtype" => $filetype));
        } else {
            return false;
        }
    }

    /**
     * @param string $external_id
     * @param string $external_url
     * @param string $title
     * @param string $filetype
     * @return bool|array
     */
    public function filesRemoteAdd($external_id, $external_url, $title, $filetype = "")
    {
        //TODO дописать загрузку файлов для $preview_img
        return self::sendGetRequest("files.remote.add", array("token" => $this->token, "external_id" => $external_id, "external_url" => $external_url, "title" => $title, "file_type" => $filetype));
    }

    /**
     * @param string $external_id
     * @param string $file
     * @return bool|array
     */
    public function filesRemoteInfo($external_id = "", $file = "")
    {
        return self::sendGetRequest("files.remote.info", array("token" => $this->token, "external_id" => $external_id, "file" => $file));
    }

    /**
     * @param string $channel
     * @param string $ts_from
     * @param string $ts_to
     * @param string $cursor
     * @param int $limit
     * @return bool|array
     */
    public function filesRemoteList($channel = "", $ts_from = "", $ts_to = "now", $cursor = "", $limit = 20)
    {
        return self::sendGetRequest("files.remote.list", array("token" => $this->token, "channel" => $channel, "ts_from" => $ts_from, "ts_to" => $ts_to, "limit" => $limit, "cursor" => $cursor));
    }

    /**
     * @param string $external_id
     * @param string $file
     * @return bool|array
     */
    public function filesRemoteRemove($external_id = "", $file = "")
    {
        return self::sendGetRequest("files.remote.remove", array("token" => $this->token, "external_id" => $external_id, "file" => $file));
    }

    /**
     * @param string $channels
     * @param string $external_id
     * @param string $file
     * @return bool|array
     */
    public function filesRemoteShare($channels, $external_id = "", $file = "")
    {
        return self::sendGetRequest("files.remote.share", array("token" => $this->token, "channels" => $channels, "external_id" => $external_id, "file" => $file));
    }

    /**
     * @param string $external_id
     * @param string $external_url
     * @param string $title
     * @param string $filetype
     * @param string $indexable_file_contents
     * @param string $preview_image
     * @return bool|array
     */
    public function filesRemoteUpdate($external_id = "", $external_url = "", $title = "", $filetype = "", $indexable_file_contents = "", $preview_image = "")
    {
        return self::sendGetRequest("files.remote.update", array("token" => $this->token, "external_id" => $external_id, "external_url" => $external_url, "title" => $title, "file_type" => $filetype, "indexable_file_contents" => $indexable_file_contents, "preview_image" => $preview_image));
    }

    /*Groups*/
    /**
     * @param string $channel
     * @return bool|array
     */
    public function groupsArchive($channel)
    {
        return self::sendPostRequest("groups.archive", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $channel
     * @param string $latest
     * @param int $oldest
     * @param bool $unreads
     * @param int $count
     * @param bool $inclusive
     * @return bool|array
     */
    public function groupsHistory($channel, $latest = "now", $oldest = 0, $unreads = false, $count = 100, $inclusive = true)
    {
        return self::sendGetRequest("groups.history", array("token" => $this->token, "channel" => $channel, "latest" => $latest, "oldest" => $oldest, "count" => $count, "unreads" => $unreads, "inclusive" => $inclusive));
    }

    /**
     * @param string $name
     * @param bool $validate
     * @return bool|array
     */
    public function groupsCreate($name, $validate = false)
    {
        return self::sendPostRequest("groups.create", array("token" => $this->token, "name" => $name, "validate" => $validate));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function groupsCreateChild($channel)
    {
        return self::sendGetRequest("groups.createChild", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $channel
     * @param bool $include_locale
     * @return bool|array
     */
    public function groupsInfo($channel, $include_locale = false)
    {
        return self::sendGetRequest("groups.info", array("token" => $this->token, "channel" => $channel, "include_locale" => $include_locale));
    }

    /**
     * @param string $channel
     * @param string $user
     * @return bool|array
     */
    public function groupsInvite($channel, $user)
    {
        return self::sendPostRequest("groups.invite", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    /**
     * @param string $channel
     * @param string $user
     * @return bool|array
     */
    public function groupsKick($channel, $user)
    {
        return self::sendPostRequest("groups.kick", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function groupsLeave($channel)
    {
        return self::sendPostRequest("groups.leave", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $cursor
     * @param bool $exclude_archived
     * @param bool $exclude_members
     * @param int $limit
     * @return bool|array
     */
    public function groupsList($cursor = "", $exclude_archived = false, $exclude_members = false, $limit = 20)
    {
        return self::sendGetRequest("groups.list", array("token" => $this->token, "cursor" => $cursor, "exclude_archived" => $exclude_archived, "exclude_members" => $exclude_members, "limit" => $limit));
    }

    /**
     * @param string $channel
     * @param float $ts
     * @return bool|array
     */
    public function groupsMark($channel, $ts)
    {
        return self::sendPostRequest("groups.mark", array("token" => $this->token, "channel" => $channel, "ts" => $ts));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function groupsOpen($channel)
    {
        return self::sendPostRequest("groups.open", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $channel
     * @param string $name
     * @param bool $validate
     * @return bool|array
     */
    public function groupsRename($channel, $name, $validate = false)
    {
        return self::sendPostRequest("groups.rename", array("token" => $this->token, "channel" => $channel, "name" => $name, "validate" => $validate));
    }

    /**
     * @param string $channel
     * @param float $thread_ts
     * @return bool|array
     */
    public function groupsReplies($channel, $thread_ts)
    {
        return self::sendGetRequest("groups.replies", array("token" => $this->token, "channel" => $channel, "thread_ts" => $thread_ts));
    }

    /**
     * @param string $channel
     * @param string $purpose
     * @return bool|array
     */
    public function groupsSetPurpose($channel, $purpose)
    {
        return self::sendPostRequest("groups.setPurpose", array("token" => $this->token, "channel" => $channel, "purpose" => $purpose));
    }

    /**
     * @param string $channel
     * @param string $topic
     * @return bool|array
     */
    public function groupsSetTopic($channel, $topic)
    {
        return self::sendPostRequest("groups.setTopic", array("token" => $this->token, "channel" => $channel, "topic" => $topic));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function groupsUnarchive($channel)
    {
        return self::sendPostRequest("groups.unarchive", array("token" => $this->token, "channel" => $channel));
    }

    /*IM*/
    /**
     * @param string $channel
     * @return bool|array
     */
    public function imClose($channel)
    {
        return self::sendPostRequest("im.close", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $channel
     * @param int $latest
     * @param int $oldest
     * @param int $count
     * @param bool $inclusive
     * @param bool $unreads
     * @return bool|array
     */
    public function imHistory($channel, $latest = 0, $oldest = 0, $count = 100, $inclusive = false, $unreads = false)
    {
        $latest = $latest = 0 ? "now" : $latest;
        return self::sendGetRequest("im.history", array("token" => $this->token, "channel" => $channel, "latest" => $latest, "oldest" => $oldest, "count" => $count, "inclusive" => $inclusive, "unreads" => $unreads));
    }

    /**
     * @param string $cursor
     * @param int $count
     * @return bool|array
     */
    public function imList($cursor = "", $count = 20)
    {
        return self::sendGetRequest("im.list", array("token" => $this->token, "cursor" => $cursor, "count" => $count));
    }

    /**
     * @param string $channel
     * @param float $ts
     * @return bool|array
     */
    public function imMark($channel, $ts)
    {
        return self::sendPostRequest("im.mark", array("token" => $this->token, "channel" => $channel, "ts" => $ts));
    }

    /**
     * @param $user
     * @param bool $include_locale
     * @param bool $return_im
     * @return bool|array
     */
    public function imOpen($user, $include_locale = false, $return_im = false)
    {
        return self::sendPostRequest("im.open", array("token" => $this->token, "user" => $user, "include_locale" => $include_locale, "return_im" => $return_im));
    }

    /**
     * @param string $channel
     * @param float $thread_ts
     * @return bool|array
     */
    public function imReplies($channel, $thread_ts)
    {
        return self::sendGetRequest("im.replies", array("token" => $this->token, "channel" => $channel, "thread_ts" => $thread_ts));
    }

    /*migration*/
    /**
     * @param string $users
     * @param bool $to_old
     * @return bool|array
     */
    public function migrationExchange($users, $to_old = false)
    {
        return self::sendGetRequest("migration.exchange", array("token" => $this->token, "users" => $users, "to_old" => $to_old));
    }

    /*mpim*/
    /**
     * @param string $channel
     * @return bool|array
     */
    public function mpimClose($channel)
    {
        return self::sendPostRequest("mpim.close", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $channel
     * @param int $latest
     * @param int $oldest
     * @param int $count
     * @param bool $inclusive
     * @param bool $unreads
     * @return bool|array
     */
    public function mpimHistory($channel, $latest = 0, $oldest = 0, $count = 100, $inclusive = false, $unreads = false)
    {
        $latest = $latest = 0 ? "now" : $latest;
        return self::sendGetRequest("mpim.history", array("token" => $this->token, "channel" => $channel, "latest" => $latest, "oldest" => $oldest, "count" => $count, "inclusive" => $inclusive, "unreads" => $unreads));
    }

    /**
     * @param string $cursor
     * @param int $count
     * @return bool|array
     */
    public function mpimList($cursor = "", $count = 20)
    {
        return self::sendGetRequest("mpim.list", array("token" => $this->token, "cursor" => $cursor, "count" => $count));
    }

    /**
     * @param string $channel
     * @param float $ts
     * @return bool|array
     */
    public function mpimMark($channel, $ts)
    {
        return self::sendPostRequest("mpim.mark", array("token" => $this->token, "channel" => $channel, "ts" => $ts));
    }

    /**
     * @param string|array $users ("W1234567890,U2345678901,U3456789012" or array("W1234567890","U2345678901","U3456789012"))
     * @return bool|array
     */
    public function mpimOpen($users)
    {
        $users = is_array($users) ? implode(",", $users) : $users;
        return self::sendPostRequest("mpim.open", array("token" => $this->token, "users" => $users));
    }

    /**
     * @param string $channel
     * @param float $thread_ts
     * @return bool|array
     */
    public function mpimReplies($channel, $thread_ts)
    {
        return self::sendGetRequest("mpim.replies", array("token" => $this->token, "channel" => $channel, "thread_ts" => $thread_ts));
    }

    /*oauth*/
    /**
     * @param string $client_id
     * @param string $client_secret
     * @param string $code
     * @param string $redirect_uri
     * @param bool $single_channel
     * @return bool|array
     */
    public function oauthAccess($client_id, $client_secret, $code, $redirect_uri = "", $single_channel = false)
    {
        return self::sendPostRequest("oauth.access", array("client_id" => $client_id, "client_secret" => $client_secret, "code" => $code, "redirect_uri" => $redirect_uri, "single_channel" => $single_channel));
    }

    /**
     * @param string $client_id
     * @param string $client_secret
     * @param string $code
     * @param string $redirect_uri
     * @param bool $single_channel
     * @return bool|array
     */
    public function oauthToken($client_id, $client_secret, $code, $redirect_uri = "", $single_channel = false)
    {
        return self::sendPostRequest("oauth.token", array("client_id" => $client_id, "client_secret" => $client_secret, "code" => $code, "redirect_uri" => $redirect_uri, "single_channel" => $single_channel));
    }

    /*pins*/
    /**
     * @param string $channel
     * @param float $timestamp
     * @return bool|array
     */
    public function pinsAdd($channel, $timestamp)
    {
        return self::sendPostRequest("pins.add", array("token" => $this->token, "channel" => $channel, "timestamp" => $timestamp));
    }

    /**
     * @param string $channel
     * @return bool|array
     */
    public function pinsList($channel)
    {
        return self::sendGetRequest("pins.list", array("token" => $this->token, "channel" => $channel));
    }

    /**
     * @param string $channel
     * @param string $file
     * @param string $file_comment
     * @param int $timestamp
     * @return bool|array
     */
    public function pinsRemove($channel, $file = "", $file_comment = "", $timestamp = 0)
    {
        return self::sendPostRequest("pins.remove", array("token" => $this->token, "channel" => $channel, "timestamp" => $timestamp, "file" => $file, "file_comment" => $file_comment));
    }

    /*reactions*/
    /**
     * @param string $channel
     * @param string $name
     * @param float $timestamp
     * @return bool|array
     */
    public function reactionsAdd($channel, $name, $timestamp)
    {
        return self::sendPostRequest("reactions.add", array("token" => $this->token, "channel" => $channel, "name" => $name, "timestamp" => $timestamp));
    }

    /**
     * @param string $channel
     * @param int $timestamp
     * @param string $file
     * @param string $file_comment
     * @param bool $full
     * @return bool|array
     */
    public function reactionsGet($channel = "", $timestamp = 0, $file = "", $file_comment = "", $full = true)
    {
        return self::sendGetRequest("reactions.get", array("token" => $this->token, "channel" => $channel, "timestamp" => $timestamp, "file" => $file, "file_comment" => $file_comment, "full" => $full));
    }

    /**
     * @param string $user
     * @param int $count
     * @param int $limit
     * @param int $page
     * @param string $cursor
     * @param bool $full
     * @return bool|array
     */
    public function reactionsList($user = "", $count = 100, $limit = 20, $page = 1, $cursor = "", $full = true)
    {
        return self::sendGetRequest("reactions.list", array("token" => $this->token, "user" => $user, "count" => $count, "limit" => $limit, "page" => $page, "cursor" => $cursor, "full" => $full));
    }

    /**
     * @param string $name
     * @param string $channel
     * @param int $timestamp
     * @param string $file
     * @param string $file_comment
     */
    public function reactionsRemove($name, $channel = "", $timestamp = 0, $file = "", $file_comment = "")
    {
        self::sendPostRequest("reactions.remove", array("token" => $this->token, "name" => $name, "channel" => $channel, "timestamp" => $timestamp, "file" => $file, "file_comment" => $file_comment));
    }

    /*reminders.add*/
    /**
     * @param string $text
     * @param float $time
     * @param string $user
     * @return bool|array
     */
    public function remindersAdd($text, $time, $user = "")
    {
        return self::sendPostRequest("reminders.add", array("token" => $this->token, "text" => $text, "time" => $time, "user" => $user));
    }

    /**
     * @param string $reminder
     * @return bool|array
     */
    public function remindersComplete($reminder)
    {
        return self::sendPostRequest("reminders.complete", array("token" => $this->token, "reminder" => $reminder));
    }

    /**
     * @param string $reminder
     * @return bool|array
     */
    public function remindersDelete($reminder)
    {
        return self::sendPostRequest("reminders.delete", array("token" => $this->token, "reminder" => $reminder));
    }

    /**
     * @param string $reminder
     * @return bool|array
     */
    public function remindersInfo($reminder)
    {
        return self::sendGetRequest("reminders.info", array("token" => $this->token, "reminder" => $reminder));
    }

    /**
     * @return bool|array
     */
    public function remindersList()
    {
        return self::sendGetRequest("reminders.list", array("token" => $this->token));
    }

    /*rtm*/
    /**
     * @param bool $batch_presence_aware
     * @param bool $presence_sub
     * @return bool|array
     */
    public function rtmConnect($batch_presence_aware = false, $presence_sub = true)
    {
        return self::sendGetRequest("rtm.connect", array("token" => $this->token, "batch_presence_aware" => $batch_presence_aware, "presence_sub" => $presence_sub));
    }

    /**
     * @param bool $batch_presence_aware
     * @param bool $include_locale
     * @param bool $mpim_aware
     * @param bool $no_latest
     * @param bool $no_unreads
     * @param bool $presence_sub
     * @param bool $simple_latest
     * @return bool|array
     */
    public function rtmStart($batch_presence_aware = false, $include_locale = false, $mpim_aware = false, $no_latest = false, $no_unreads = false, $presence_sub = true, $simple_latest = true, $presence_sub = true)
    {
        return self::sendGetRequest("rtm.start", array("token" => $this->token, "batch_presence_aware" => $batch_presence_aware, "presence_sub" => $presence_sub, "include_locale" => $include_locale, "mpim_aware" => $mpim_aware, "no_latest" => $no_latest, "no_unreads" => $no_unreads, "simple_latest" => $simple_latest,));
    }

    /*search*/
    /**
     * @param string $query
     * @param int $count
     * @param int $page
     * @param string $sort
     * @param string $sort_dir
     * @param bool $highlight
     * @return bool|array
     */
    public function searchAll($query, $count = 20, $page = 1, $sort = "score", $sort_dir = "desc", $highlight = false)
    {
        return self::sendGetRequest("search.all", array("token" => $this->token, "query" => $query, "count" => $count, "page" => $page, "sort" => $sort, "sort_dir" => $sort_dir, "highlight" => $highlight));
    }

    /**
     * @param string $query
     * @param int $count
     * @param int $page
     * @param string $sort
     * @param string $sort_dir
     * @param bool $highlight
     * @return bool|array
     */
    public function searchFiles($query, $count = 20, $page = 1, $sort = "score", $sort_dir = "desc", $highlight = false)
    {
        return self::sendGetRequest("search.files", array("token" => $this->token, "query" => $query, "count" => $count, "page" => $page, "sort" => $sort, "sort_dir" => $sort_dir, "highlight" => $highlight));
    }

    /**
     * @param string $query
     * @param int $count
     * @param int $page
     * @param string $sort
     * @param string $sort_dir
     * @param bool $highlight
     * @return bool|array
     */
    public function searchMessages($query, $count = 20, $page = 1, $sort = "score", $sort_dir = "desc", $highlight = false)
    {
        return self::sendGetRequest("search.messages", array("token" => $this->token, "query" => $query, "count" => $count, "page" => $page, "sort" => $sort, "sort_dir" => $sort_dir, "highlight" => $highlight));
    }

    /*stars*/
    /**
     * @param string $channel
     * @param int $timestamp
     * @param string $file
     * @param string $file_comment
     * @return bool|array
     */
    public function starsAdd($channel = "", $timestamp = 0, $file = "", $file_comment = "")
    {
        return self::sendPostRequest("stars.add", array("token" => $this->token, "channel" => $channel, "timestamp" => $timestamp, "file" => $file, "file_comment" => $file_comment));
    }

    /**
     * @param string $cursor
     * @param int $count
     * @param int $limit
     * @param int $page
     * @return bool|array
     */
    public function starsList($cursor = "", $count = 100, $limit = 0, $page = 1)
    {
        return self::sendGetRequest("stars.list", array("token" => $this->token, "cursor" => $cursor, "count" => $count, "limit" => $limit, "page" => $page));
    }

    /**
     * @param string $channel
     * @param float $timestamp
     * @param string $file
     * @param string $file_comment
     * @return bool|array
     */
    public function starsRemove($channel = "", $timestamp = 0.0, $file = "", $file_comment = "")
    {
        $timestamp = $timestamp = 0.0 ? 0 : $timestamp;
        return self::sendPostRequest("stars.remove", array("token" => $this->token, "channel" => $channel, "timestamp" => $timestamp, "file" => $file, "file_comment" => $file_comment));
    }

    /*team*/
    /**
     * @param int $before
     * @param int $page
     * @param int $count
     * @return bool|array
     */
    public function teamAccessLogs($before = 0, $page = 1, $count = 100)
    {
        $before = $before = 0 ? "now" : $before;
        return self::sendGetRequest("team.accessLogs", array("token" => $this->token, "before" => $before, "page" => $page, "count" => $count));
    }

    /**
     * @param string $user
     * @return bool|array
     */
    public function teamBillableInfo($user)
    {
        return self::sendGetRequest("team.billableInfo", array("token" => $this->token, "user" => $user));
    }

    /**
     * @param string $team
     * @return bool|array
     */
    public function teamInfo($team = "")
    {
        return self::sendGetRequest("team.info", array("token" => $this->token, "team" => $team));
    }

    /**
     * @param string $user
     * @param string $app_id
     * @param string $change_type
     * @param int $count
     * @param int $page
     * @param string $service_id
     * @return bool|array
     */
    public function teamIntegrationLogs($user = "", $app_id = "", $change_type = "added", $count = 100, $page = 1, $service_id = "")
    {
        return self::sendGetRequest("team.integrationLogs", array("token" => $this->token, "user" => $user, "app_id" => $app_id, "change_type" => $change_type, "count" => $count, "page" => $page, "service_id" => $service_id));
    }

    /**
     * @param string $visibility
     * @return bool|array
     */
    public function teamProfileGet($visibility = "all")
    {
        return self::sendGetRequest("team.profile.get", array("token" => $this->token, "visibility" => $visibility));
    }

    /*usergroups*/
    /**
     * @param string $name
     * @param string $channels
     * @param string $description
     * @param string $handle
     * @param bool $include_count
     * @return bool|array
     */
    public function usergroupsCreate($name, $channels = "", $description = "", $handle = "", $include_count = false)
    {
        return self::sendPostRequest("usergroups.create", array("token" => $this->token, "name" => $name, "channels" => $channels, "description" => $description, "handle" => $handle, "include_count" => $include_count));
    }

    /**
     * @param string $usergroup
     * @param bool $include_count
     * @return bool|array
     */
    public function usergroupsDisable($usergroup, $include_count = false)
    {
        return self::sendPostRequest("usergroups.disable", array("token" => $this->token, "usergroup" => $usergroup, "include_count" => $include_count));
    }

    /**
     * @param string $usergroup
     * @param bool $include_count
     * @return bool|array
     */
    public function usergroupsEnable($usergroup, $include_count = false)
    {
        return self::sendPostRequest("usergroups.enable", array("token" => $this->token, "usergroup" => $usergroup, "include_count" => $include_count));
    }

    /**
     * @param bool $include_count
     * @param bool $include_disabled
     * @param bool $include_users
     * @return bool|array
     */
    public function usergroupsList($include_count = false, $include_disabled = false, $include_users = false)
    {
        return self::sendGetRequest("usergroups.list", array("token" => $this->token, "include_count" => $include_count, "include_disabled" => $include_disabled, "include_users" => $include_users));
    }

    /**
     * @param string $usergroup
     * @param string $channels
     * @param string $description
     * @param string $name
     * @param string $handle
     * @param bool $include_count
     * @return bool|array
     */
    public function usergroupsUpdate($usergroup, $channels = "", $description = "", $name = "", $handle = "", $include_count = false)
    {
        return self::sendPostRequest("usergroups.update", array("token" => $this->token, "usergroup" => $usergroup, "channels" => $channels, "description" => $description, "name" => $name, "handle" => $handle, "include_count" => $include_count));
    }

    /*usergroups.users*/
    /**
     * @param string $usergroup
     * @param bool $include_disabled
     * @return bool|array
     */
    public function usergroupsUsersList($usergroup, $include_disabled = false)
    {
        return self::sendGetRequest("usergroups.users.list", array("token" => $this->token, "usergroup" => $usergroup, "include_disabled" => $include_disabled));
    }

    /**
     * @param string $usergroup
     * @param string|array $users
     * @param bool $include_count
     * @return bool|array
     */
    public function usergroupsUsersUpdate($usergroup, $users, $include_count = false)
    {
        $users = is_array($users) ? implode(",", $users) : $users;
        return self::sendPostRequest("usergroups.users.update", array("token" => $this->token, "usergroup" => $usergroup, "users" => $users, "include_count" => $include_count));
    }

    /*users*/
    /**
     * @param string $user
     * @param string $cursor
     * @param bool $exclude_archived
     * @param int $limit
     * @param string $types
     * @return bool|array
     */
    public function usersConversations($user = "", $cursor = "", $exclude_archived = false, $limit = 100, $types = "public_channel")
    {
        return self::sendGetRequest("users.conversations", array("token" => $this->token, "user" => $user, "cursor" => $cursor, "exclude_archived" => $exclude_archived, "limit" => $limit, "types" => $types));
    }

    /**
     * @return bool|array
     */
    public function usersDeletePhoto()
    {
        return self::sendGetRequest("users.deletePhoto", array("token" => $this->token));
    }

    /**
     * @param string $user
     * @return bool|array
     */
    public function usersGetPresence($user)
    {
        return self::sendGetRequest("users.getPresence", array("token" => $this->token, "user" => $user));
    }

    /**
     * @return bool|array
     */
    public function usersIdentity()
    {
        return self::sendGetRequest("users.identity", array("token" => $this->token));
    }

    /**
     * @param string $user
     * @param bool $include_locale
     * @return bool|array
     */
    public function usersInfo($user, $include_locale = false)
    {
        return self::sendGetRequest("users.info", array("token" => $this->token, "user" => $user, "include_locale" => $include_locale));
    }

    /**
     * @param string $cursor
     * @param int $limit
     * @param bool $include_locale
     * @return bool|array
     */
    public function usersList($cursor = "", $limit = 0, $include_locale = false)
    {
        return self::sendGetRequest("users.list", array("token" => $this->token, "cursor" => $cursor, "limit" => $limit, "include_locale" => $include_locale));
    }

    /**
     * @param string $email
     * @return bool|array
     */
    public function usersLookupByEmail($email)
    {
        return self::sendGetRequest("users.lookupByEmail", array("token" => $this->token, "email" => $email));
    }

    /**
     * @return bool|array
     */
    public function usersSetActive()
    {
        return self::sendPostRequest("users.setActive", array("token" => $this->token));
    }

    /**
     * @param string $image
     * @param int $crop_w
     * @param int $crop_x
     * @param int $crop_y
     * @return false
     */
    public function usersSetPhoto($image, $crop_w = 0, $crop_x = 0, $crop_y = 0)
    {
        //TODO добавить multipart отправку фото на сервер slack
        return false;
    }

    /**
     * @param $presence
     * @return bool|array
     */
    public function usersSetPresence($presence)
    {
        return self::sendPostRequest("users.setPresence", array("token" => $this->token, "presence" => $presence));
    }

    /**
     * @param string $user
     * @param bool $include_labels
     * @return bool|array
     */
    public function usersProfileGet($user, $include_labels = false)
    {
        return self::sendGetRequest("users.profile.get", array("token" => $this->token, "user" => $user, "include_labels" => $include_labels));
    }

    /**
     * @param string $name
     * @param array $profile
     * @param string $user
     * @param string $value
     * @return bool|array
     */
    public function usersProfileSet($name = "", $profile = array(), $user = "", $value = "")
    {

        return self::sendPostRequest("users.profile.set", array("token" => $this->token, "name" => $name, "profile" => $profile, "user" => $user, "value" => $value));
    }

    /*views*/
    /**
     * @param string $trigger_id
     * @param string $view
     * @return bool|array
     */
    public function viewsOpen($trigger_id, $view)
    {
        return self::sendPostRequest("views.open", array("token" => $this->token, "trigger_id" => $trigger_id, "view" => $view));
    }

    /**
     * @param string $trigger_id
     * @param string $view
     * @return bool|array
     */
    public function viewsPush($trigger_id, $view)
    {
        return self::sendPostRequest("views.push", array("token" => $this->token, "trigger_id" => $trigger_id, "view" => $view));
    }

    /**
     * @param string $view
     * @param string $view_id
     * @param string $external_id
     * @param string $hash
     * @return bool|mixed
     */
    public function viewsUpdate($view, $view_id = "", $external_id = "", $hash = "")
    {
        return self::sendPostRequest("views.update", array("token" => $this->token, "view" => $view, "view_id" => $view_id, "external_id" => $external_id, "hash" => $hash));
    }
}