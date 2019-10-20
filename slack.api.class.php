<?php

class slackApi
{
    protected $token;
    protected $request_status;
    protected $request_response;
    protected $error;

    public function __construct($token)
    {
        $this->token = $token;
    }

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

    public function adminAppsRequestsList($limit = 100, $cursor = "", $team_id = "")
    {
        return self::sendGetRequest("admin.apps.requests.list", array("token" => $this->token, "limit" => $limit, "cursor" => $cursor, "team_id" => $team_id));
    }

    public function adminUsersSessionReset($user_id, $mobile_only = false, $web_only = false)
    {
        return self::sendPostRequest("admin.users.session.reset", array("token" => $this->token, "user_id" => $user_id, "mobile_only" => $mobile_only, "web_only" => $web_only));
    }

    public function apiTest($error = "", $external_param = "")
    {
        return self::sendPostRequest("api.test", array("token" => $this->token, "error" => $error, "foo" => $external_param));
    }

    public function appsPermissionsInfo()
    {
        return self::sendGetRequest("apps.permissions.info", array("token" => $this->token));
    }

    public function appsPermissionsRequest($scopes, $trigger_id = "")
    {
        return self::sendGetRequest("apps.permissions.request", array("token" => $this->token, "scopes" => $scopes, "trigger_id" => $trigger_id));
    }

    public function appsPermissionsResourcesList($cursor, $limit = 20)
    {
        return self::sendGetRequest("apps.permissions.resources.list", array("token" => $this->token, "cursor" => $cursor, "limit" => $limit));
    }

    public function appsPermissionsScopesList()
    {
        return self::sendGetRequest("apps.permissions.scopes.list", array("token" => $this->token));
    }

    public function appsPermissionsUsersList($cursor = "", $limit = 20)
    {
        return self::sendGetRequest("apps.permissions.resources.list", array("token" => $this->token, "cursor" => $cursor, "limit" => $limit));
    }

    public function appsPermissionsUsersRequest($user, $scopes, $trigger_id)
    {
        return self::sendGetRequest("apps.permissions.users.request", array("token" => $this->token, "user" => $user, "scopes" => $scopes, "trigger_id" => $trigger_id));
    }

    public function appsUninstall($client_id, $client_secret)
    {
        return self::sendGetRequest("apps.uninstall", array("token" => $this->token, "client_id" => $client_id, "client_secret" => $client_secret));
    }

    public function authRevoke($test = false)
    {
        return self::sendGetRequest("auth.revoke", array("token" => $this->token, "test" => $test));
    }

    public function authTest()
    {
        return self::sendGetRequest("auth.test", array("token" => $this->token));
    }

    public function botsInfo($bot = "")
    {
        return self::sendGetRequest("bots.info", array("token" => $this->token, "bot" => $bot));
    }

    /*Channels methods*/
    public function channelsArchive($channel)
    {
        return self::sendPostRequest("channels.archive", array("token" => $this->token, "channel" => $channel));
    }

    public function channelsCreate($name, $validate = true)
    {
        return self::sendPostRequest("channels.create", array("token" => $this->token, "name" => $name, "validate" => $validate));
    }

    public function channelsHistory($channel, $latest = "", $oldest = "", $inclusive = true, $count = 100, $unreads = false)
    {
        return self::sendGetRequest("channels.history", array("token" => $this->token, "channel" => $channel, "latest" => $latest, "oldest" => $oldest, "inclusive" => $inclusive, "count" => $count, "unreads" => $unreads));
    }

    public function channelsInfo($channel)
    {
        return self::sendGetRequest("channels.info", array("token" => $this->token, "channel" => $channel));
    }

    public function channelsInvite($channel, $user)
    {
        return self::sendPostRequest("channels.invite", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    public function channelsJoin($name, $validate = true)
    {
        return self::sendPostRequest("channels.join", array("token" => $this->token, "name" => $name, "validate" => $validate));
    }

    public function channelsKick($channel, $user)
    {
        return self::sendPostRequest("channels.kick", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    public function channelsLeave($channel)
    {
        return self::sendPostRequest("channels.leave", array("token" => $this->token, "channel" => $channel));
    }

    public function channelsMark($channel, $ts)
    {
        return self::sendPostRequest("channels.mark", array("token" => $this->token, "channel" => $channel, "ts" => $ts));
    }

    public function channelsRename($channel, $name, $validate = true)
    {
        return self::sendPostRequest("channels.rename", array("token" => $this->token, "channel" => $channel, "name" => $name, "validate" => $validate));
    }

    public function channelsReplies($channel, $thread_ts)
    {
        return self::sendGetRequest("channels.replies", array("token" => $this->token, "channel" => $channel, "thread_ts" => $thread_ts));
    }

    public function channelsSetPurpose($channel, $purpose, $name_tagging = true)
    {
        return self::sendPostRequest("channels.setPurpose", array("token" => $this->token, "channel" => $channel, "purpose" => $purpose, "name_tagging" => $name_tagging));
    }

    public function channelsSetTopic($channel, $topic)
    {
        return self::sendPostRequest("channels.setTopic", array("token" => $this->token, "channel" => $channel, "topic" => $topic));
    }

    public function channelsUnarchive($channel)
    {
        return self::sendPostRequest("channels.unarchive", array("token" => $this->token, "channel" => $channel));
    }

    public function channelsList($cursor = "")
    {
        return self::sendGetRequest("channels.list", array("token" => $this->token, "cursor" => $cursor));
    }

    public function chatDelete($channel, $ts, $as_user = false)
    {
        return self::sendPostRequest("chat.delete", array("token" => $this->token, "channel" => $channel, "ts" => $ts, "as_user" => $as_user));
    }

    public function chatDeleteScheduledMessage($channel, $scheduled_message_id, $as_user = false)
    {
        return self::sendPostRequest("chat.deleteScheduledMessage", array("token" => $this->token, "channel" => $channel, "scheduled_message_id" => $scheduled_message_id, "as_user" => $as_user));
    }

    public function chatGetPermalink($channel, $message_ts)
    {
        return self::sendPostRequest("chat.getPermalink", array("token" => $this->token, "channel" => $channel, "message_ts" => $message_ts));
    }

    /*Chats methods*/
    public function chatMeMessage($channel, $message)
    {
        return self::sendPostRequest("chat.meMessage", array("token" => $this->token, "channel" => $channel, "message_ts" => $message));
    }

    public function chatPostEphemeral($channel, $text, $attachments, $user, $as_user)
    {
        return self::sendPostRequest("chat.postEphemeral", array("token" => $this->token, "channel" => $channel, "text" => $text, "attachments" => $attachments, "user" => $user, "as_user" => $as_user));
    }

    public function chatPostMessage($channel, $as_user = false, $thread_ts = "", $text = "", $attachments = array(), $blocks = array(), $icon_emoji = "", $icon_url = "", $link_names = true, $mrkdwn = false, $parse = "full", $reply_broadcast = true, $unfurl_links = true, $unfurl_media = false, $username = "")
    {
        return self::sendPostRequest("chat.postMessage", array("token" => $this->token, "channel" => $channel, "text" => $text, "attachments" => $attachments, "blocks" => $blocks, "icon_emoji" => $icon_emoji, "as_user" => $as_user, "icon_url" => $icon_url, "link_names" => $link_names, "mrkdwn" => $mrkdwn, "parse" => $parse, "reply_broadcast" => $reply_broadcast, "thread_ts" => $thread_ts, "unfurl_links" => $unfurl_links, "unfurl_media" => $unfurl_media, "username" => $username));
    }

    public function chatScheduleMessage($channel, $post_at, $text = "", $attachments = array(), $blocks = array(), $as_user = false, $thread_ts = "", $link_names = true, $parse = "full", $reply_broadcast = true, $unfurl_links = true, $unfurl_media = false)
    {
        return self::sendPostRequest("chat.scheduleMessage", array("token" => $this->token, "channel" => $channel, "text" => $text, "attachments" => $attachments, "blocks" => $blocks, "link_names" => $link_names, "parse" => $parse, "reply_broadcast" => $reply_broadcast, "thread_ts" => $thread_ts, "unfurl_links" => $unfurl_links, "unfurl_media" => $unfurl_media, "as_user" => $as_user, "post_at" => $post_at));
    }

    public function chatUnfurl($channel, $ts, $unfurls, $user_auth_message = "", $user_auth_required = false, $user_auth_url = "")
    {
        return self::sendPostRequest("chat.unfurl", array("token" => $this->token, "channel" => $channel, "ts" => $ts, "unfurls" => $unfurls, "user_auth_message" => $user_auth_message, "user_auth_required" => $user_auth_required, "user_auth_url" => $user_auth_url));
    }

    public function chatUpdate($channel, $text, $ts, $as_user = false, $attachments = array(), $blocks = array(), $link_names = true, $parse = "none")
    {
        return self::sendPostRequest("chat.update", array("token" => $this->token, "channel" => $channel, "text" => $text, "ts" => $ts, "as_user" => $as_user, "attachments" => $attachments, "blocks" => $blocks, "link_names" => $link_names, "parse" => $parse));
    }

    public function chatScheduledMessagesList($channel, $latest = "", $oldest = "", $limit = 100, $cursor = "")
    {
        return self::sendPostRequest("chat.scheduledMessages.list", array("token" => $this->token, "channel" => $channel, "latest" => $latest, "oldest" => $oldest, "limit" => $limit, "cursor" => $cursor));
    }

    /*Conversations methods*/
    public function conversationsArchive($channel)
    {
        return self::sendPostRequest("conversations.archive", array("token" => $this->token, "channel" => $channel));
    }

    public function conversationsClose($channel)
    {
        return self::sendPostRequest("conversations.close", array("token" => $this->token, "channel" => $channel));
    }

    public function conversationsCreate($name, $is_private = false, $is_private = "")
    {
        return self::sendPostRequest("conversations.create", array("token" => $this->token, "$name" => $name, "is_private" => $is_private));
    }

    public function conversationsHistory($channel, $cursor = "", $inclusive = true, $latest = "", $oldest = "", $limit = 20)
    {
        return self::sendGetRequest("conversations.history", array("token" => $this->token, "channel" => $channel, "cursor" => $cursor, "inclusive" => $inclusive, "latest" => $latest, "oldest" => $oldest, "limit" => $limit));
    }

    public function conversationsInfo($channel, $include_locale = true, $include_num_members = true)
    {
        return self::sendGetRequest("conversationsI.info", array("token" => $this->token, "channel" => $channel, "include_locale" => $include_locale, "include_num_members" => $include_num_members));
    }

    public function conversationsInvite($channel, $user)
    {
        return self::sendPostRequest("conversations.invite", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    public function conversationsJoin($channel)
    {
        return self::sendPostRequest("conversations.join", array("token" => $this->token, "channel" => $channel));
    }

    public function conversationsKick($channel, $user)
    {
        return self::sendPostRequest("conversations.kick", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    public function conversationsList($cursor = "", $exclude_archived = true, $limit = 20, $types = "")
    {
        return self::sendGetRequest("conversations.list", array("token" => $this->token, "cursor" => $cursor, "exclude_archived" => $exclude_archived, "limit" => $limit, "types" => $types));
    }

    public function conversationsMembers($channel, $limit = 20, $cursor = "")
    {
        return self::sendGetRequest("conversations.members", array("token" => $this->token, "channel" => $channel, "limit" => $limit, "cursor" => $cursor));
    }

    public function conversationsOpen($channel = "", $return_im = "", $users = "")
    {
        return self::sendPostRequest("conversations.open", array("token" => $this->token, "channel" => $channel, "return_im" => $return_im, "users" => $users));
    }

    public function conversationsRename($channel, $name)
    {
        return self::sendPostRequest("conversations.rename", array("token" => $this->token, "channel" => $channel, "name" => $name));
    }

    public function conversationsReplies($channel, $ts, $cursor = "", $inclusive = true, $latest = "", $oldest = "", $limit = 20)
    {
        return self::sendGetRequest("conversations.replies", array("token" => $this->token, "channel" => $channel, "ts" => $ts, "cursor" => $cursor, "inclusive" => $inclusive, "latest" => $latest, "oldest" => $oldest, "limit" => $limit));
    }

    public function conversationsSetPurpose($channel, $purpose)
    {
        return self::sendPostRequest("conversations.setPurpose", array("token" => $this->token, "channel" => $$channel, "purpose" => $purpose));
    }

    public function conversationsSetTopic($channel, $topic)
    {
        return self::sendPostRequest("conversations.setTopic", array("token" => $this->token, "channel" => $channel, "topic" => $topic));
    }

    public function conversationsUnarchive($channel)
    {
        return self::sendPostRequest("conversations.unarchive", array("token" => $this->token, "channel" => $channel));
    }

    /*Dialogs methods*/
    public function dialogOpen($dialog, $trigger_id)
    {
        return self::sendPostRequest("dialog.open", array("token" => $this->token, "dialog" => $dialog, "trigger_id" => $trigger_id));
    }

    public function dndEndDnd()
    {
        return self::sendPostRequest("dnd.endDnd", array("token" => $this->token));
    }

    public function dndEndSnooze()
    {
        return self::sendPostRequest("dnd.endSnooze", array("token" => $this->token));
    }

    public function dndInfo($user = "")
    {
        return self::sendPostRequest("dnd.info", array("token" => $this->token, "user" => $user));
    }

    public function dndSetSnooze($num_minutes = 60)
    {
        return self::sendPostRequest("dnd.setSnooze", array("token" => $this->token, "num_minutes" => $num_minutes));
    }

    public function dndTeamInfo($users = "")
    {
        return self::sendGetRequest("dnd.teamInfo", array("token" => $this->token, "users" => $users));
    }

    public function emojiList()
    {
        return self::sendGetRequest("emoji.list", array("token" => $this->token));
    }

    public function filesCommentsDelete($file, $id)
    {
        return self::sendPostRequest("files.commentsDelete", array("token" => $this->token, "file" => $file, "id" => $id));
    }

    /*Files*/
    public function filesDelete($file)
    {
        return self::sendPostRequest("files.delete", array("token" => $this->token, "file" => $file));
    }

    public function filesInfo($file, $count = 100, $cursor = "")
    {
        return self::sendGetRequest("files.info", array("token" => $this->token, "file" => $file, "count" => $count, "cursor" => $cursor));
    }

    public function filesList($channel = "", $ts_from = 0, $ts_to = "now", $count = 20, $page = 1, $types = "all", $user = "")
    {
        return self::sendGetRequest("files.list", array("token" => $this->token, "channel" => $channel, "count" => $count, "page" => $page, "ts_from" => $ts_from, "ts_to" => $ts_to, "types" => $types, "user" => $user));
    }

    public function filesRevokePublicURL($file)
    {
        return self::sendPostRequest("files.revokePublicURL", array("token" => $this->token, "file" => $file));
    }

    public function filesSharedPublicURL($file)
    {
        return self::sendPostRequest("files.sharedPublicURL", array("token" => $this->token, "file" => $file));
    }

    public function filesUpload($json = true, $channels = "", $initial_comment = "", $title = "", $content = "", $file = "", $thread_ts = "", $filename = "", $filetype = "")
    {
        //TODO Расширить под два варината
        if ($json) {
            return self::sendPostRequest("files.upload", array("token" => $this->token, "channels" => $channels, "initial_comment" => $initial_comment, "title" => $title, "content" => $content, "thread_ts" => $thread_ts, "filename" => $filename, "filtype" => $filetype));
        } else {
            return false;
        }
    }

    public function filesRemoteAdd($external_id, $external_url, $title, $filetype = "", $indexable_file_contents = "", $preview_image = "")
    {
        //TODO Пересмотреть метод
        return self::sendGetRequest("files.remote.add", array("token" => $this->token, "external_id" => $external_id, "external_url" => $external_url, "title" => $title, "file_type" => $filetype, "indexable_file_contents" => $indexable_file_contents, "preview_image" => $preview_image));
    }

    public function filesRemoteInfo($external_id = "", $file = "")
    {
        return self::sendGetRequest("files.remote.info", array("token" => $this->token, "external_id" => $external_id, "file" => $file));
    }

    public function filesRemoteList($channel = "", $ts_from = "", $ts_to = "now", $cursor = "", $limit = 20)
    {
        return self::sendGetRequest("files.remote.list", array("token" => $this->token, "channel" => $channel, "ts_from" => $ts_from, "ts_to" => $ts_to, "limit" => $limit, "cursor" => $cursor));
    }

    public function filesRemoteRemove($external_id = "", $file = "")
    {
        return self::sendGetRequest("files.remote.remove", array("token" => $this->token, "external_id" => $external_id, "file" => $file));
    }

    public function filesRemoteShare($channels, $external_id = "", $file = "")
    {
        return self::sendGetRequest("files.remote.share", array("token" => $this->token, "channels" => $channels, "external_id" => $external_id, "file" => $file));
    }

    public function filesRemoteUpdate($external_id = "", $external_url = "", $title = "", $filetype = "", $indexable_file_contents = "", $preview_image = "")
    {
        return self::sendGetRequest("files.remote.update", array("token" => $this->token, "external_id" => $external_id, "external_url" => $external_url, "title" => $title, "file_type" => $filetype, "indexable_file_contents" => $indexable_file_contents, "preview_image" => $preview_image));
    }

    /*Groups*/
    public function groupsArchive($channel)
    {
        return self::sendPostRequest("groups.archive", array("token" => $this->token, "channel" => $channel));
    }

    public function groupsHistory($channel, $latest = "now", $oldest = 0, $unreads = false, $count = 100, $inclusive = true)
    {
        return self::sendGetRequest("groups.history", array("token" => $this->token, "channel" => $channel, "latest" => $latest, "oldest" => $oldest, "count" => $count, "unreads" => $unreads, "inclusive" => $inclusive));
    }

    public function groupsCreate($name, $validate = false)
    {
        return self::sendPostRequest("groups.create", array("token" => $this->token, "name" => $name, "validate" => $validate));
    }

    public function groupsCreateChild($channel)
    {
        return self::sendGetRequest("groups.createChild", array("token" => $this->token, "channel" => $channel));
    }

    public function groupsInfo($channel, $include_locale = false)
    {
        return self::sendGetRequest("groups.info", array("token" => $this->token, "channel" => $channel, "include_locale" => $include_locale));
    }

    public function groupsInvite($channel, $user)
    {
        return self::sendPostRequest("groups.invite", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    public function groupsKick($channel, $user)
    {
        return self::sendPostRequest("groups.kick", array("token" => $this->token, "channel" => $channel, "user" => $user));
    }

    public function groupsLeave($channel)
    {
        return self::sendPostRequest("groups.leave", array("token" => $this->token, "channel" => $channel));
    }

    public function groupsList($cursor = "", $exclude_archived = false, $exclude_members = false, $limit = 20)
    {
        return self::sendGetRequest("groups.list", array("token" => $this->token, "cursor" => $cursor, "exclude_archived" => $exclude_archived, "exclude_members" => $exclude_members, "limit" => $limit));
    }

    public function groupsMark($channel, $ts)
    {
        return self::sendPostRequest("groups.mark", array("token" => $this->token, "channel" => $channel, "ts" => $ts));
    }

    public function groupsOpen($channel)
    {
        return self::sendPostRequest("groups.open", array("token" => $this->token, "channel" => $channel));
    }

    public function groupsRename($channel, $name, $validate = false)
    {
        return self::sendPostRequest("groups.rename", array("token" => $this->token, "channel" => $channel, "name" => $name, "validate" => $validate));
    }

    public function groupsReplies($channel, $thread_ts)
    {
        return self::sendGetRequest("groups.replies", array("token" => $this->token, "channel" => $channel, "thread_ts" => $thread_ts));
    }

    public function groupsSetPurpose($channel, $purpose)
    {
        return self::sendPostRequest("groups.setPurpose", array("token" => $this->token, "channel" => $channel, "purpose" => $purpose));
    }

    public function groupsSetTopic($channel, $topic)
    {
        return self::sendPostRequest("groups.setTopic", array("token" => $this->token, "channel" => $channel, "topic" => $topic));
    }

    public function groupsUnarchive($channel)
    {
        return self::sendPostRequest("groups.unarchive", array("token" => $this->token, "channel" => $channel));
    }

    /*IM*/
    public function imClose($channel)
    {
        return self::sendPostRequest("im.close", array("token" => $this->token, "channel" => $channel));
    }

    public function imHistory($channel, $latest = 0, $oldest = 0, $count = 100, $inclusive = false, $unreads = false)
    {
        $latest = $latest = 0 ? "now" : $latest;
        return self::sendGetRequest("im.history", array("token" => $this->token, "channel" => $channel, "latest" => $latest, "oldest" => $oldest, "count" => $count, "inclusive" => $inclusive, "unreads" => $unreads));
    }

    public function imList($cursor = "", $count = 20)
    {
        return self::sendGetRequest("im.list", array("token" => $this->token, "cursor" => $cursor, "count" => $count));
    }

    public function imMark($channel, $ts)
    {
        return self::sendPostRequest("im.mark", array("token" => $this->token, "channel" => $channel, "ts" => $ts));
    }

    public function imOpen($user, $include_locale = false, $return_im = false)
    {
        return self::sendPostRequest("im.open", array("token" => $this->token, "user" => $user, "include_locale" => $include_locale, "return_im" => $return_im));
    }

    public function imReplies($channel, $thread_ts)
    {
        return self::sendGetRequest("im.replies", array("token" => $this->token, "channel" => $channel, "thread_ts" => $thread_ts));
    }

    /*migration*/
    public function migrationExchange($users, $to_old = false)
    {
        return self::sendGetRequest("migration.exchange", array("token" => $this->token, "users" => $users, "to_old" => $to_old));
    }

    /*mpim*/
    public function mpimClose($channel)
    {
        return self::sendPostRequest("mpim.close", array("token" => $this->token, "channel" => $channel));
    }

    public function mpimHistory($channel, $latest = 0, $oldest = 0, $count = 100, $inclusive = false, $unreads = false)
    {
        $latest = $latest = 0 ? "now" : $latest;
        return self::sendGetRequest("mpim.history", array("token" => $this->token, "channel" => $channel, "latest" => $latest, "oldest" => $oldest, "count" => $count, "inclusive" => $inclusive, "unreads" => $unreads));
    }

    public function mpimList($cursor = "", $count = 20)
    {
        return self::sendGetRequest("mpim.list", array("token" => $this->token, "cursor" => $cursor, "count" => $count));
    }

    public function mpimMark($channel, $ts)
    {
        return self::sendPostRequest("mpim.mark", array("token" => $this->token, "channel" => $channel, "ts" => $ts));
    }

    public function mpimOpen($users)
    {
        return self::sendPostRequest("mpim.open", array("token" => $this->token, "users" => $users));
    }

    public function mpimReplies($channel, $thread_ts)
    {
        return self::sendGetRequest("mpim.replies", array("token" => $this->token, "channel" => $channel, "thread_ts" => $thread_ts));
    }

    /*oauth*/
    public function oauthAccess($client_id, $client_secret, $code, $redirect_uri = "", $single_channel = false)
    {
        return self::sendPostRequest("oauth.access", array("client_id" => $client_id, "client_secret" => $client_secret, "code" => $code, "redirect_uri" => $redirect_uri, "single_channel" => $single_channel));
    }

    public function oauthToken($client_id, $client_secret, $code, $redirect_uri = "", $single_channel = false)
    {
        return self::sendPostRequest("oauth.token", array("client_id" => $client_id, "client_secret" => $client_secret, "code" => $code, "redirect_uri" => $redirect_uri, "single_channel" => $single_channel));
    }

    /*pins*/
    public function pinsAdd($channel, $timestamp)
    {
        return self::sendPostRequest("pins.add", array("token" => $this->token, "channel" => $channel, "timestamp" => $timestamp));
    }

    public function pinsList($channel)
    {
        return self::sendGetRequest("pins.list", array("token" => $this->token, "channel" => $channel));
    }

    public function pinsRemove($channel, $file = "", $file_comment = "", $timestamp = 0)
    {
        return self::sendPostRequest("pins.remove", array("token" => $this->token, "channel" => $channel, "timestamp" => $timestamp, "file" => $file, "file_comment" => $file_comment));
    }

    /*reactions*/
    public function reactionsAdd($channel, $name, $timestamp)
    {
        return self::sendPostRequest("reactions.add", array("token" => $this->token, "channel" => $channel, "name" => $name, "timestamp" => $timestamp));
    }

    public function reactionsGet($channel = "", $timestamp = 0, $file = "", $file_comment = "", $full = true)
    {
        return self::sendGetRequest("reactions.get", array("token" => $this->token, "channel" => $channel, "timestamp" => $timestamp, "file" => $file, "file_comment" => $file_comment, "full" => $full));
    }

    public function reactionsList($user = "", $count = 100, $limit = 20, $page = 1, $cursor = "", $full = true)
    {
        return self::sendGetRequest("reactions.list", array("token" => $this->token, "user" => $user, "count" => $count, "limit" => $limit, "page" => $page, "cursor" => $cursor, "full" => $full));
    }

    public function reactionsRemove($name, $channel = "", $timestamp = 0, $file = "", $file_comment = "")
    {
        self::sendPostRequest("reactions.remove", array("token" => $this->token, "name" => $name, "channel" => $channel, "timestamp" => $timestamp, "file" => $file, "file_comment" => $file_comment));
    }

    /*reminders.add*/
    public function remindersAdd($text, $time, $user = "")
    {
        return self::sendPostRequest("reminders.add", array("token" => $this->token, "text" => $text, "time" => $time, "user" => $user));
    }

    public function remindersComplete($reminder)
    {
        return self::sendPostRequest("reminders.complete", array("token" => $this->token, "reminder" => $reminder));
    }

    public function remindersDelete($reminder)
    {
        return self::sendPostRequest("reminders.delete", array("token" => $this->token, "reminder" => $reminder));
    }

    public function remindersInfo($reminder)
    {
        return self::sendGetRequest("reminders.info", array("token" => $this->token, "reminder" => $reminder));
    }

    public function remindersList()
    {
        return self::sendGetRequest("reminders.list", array("token" => $this->token));
    }

    /*rtm*/
    public function rtmConnect($batch_presence_aware = false, $presence_sub = true)
    {
        return self::sendGetRequest("rtm.connect", array("token" => $this->token, "batch_presence_aware" => $batch_presence_aware, "presence_sub" => $presence_sub));
    }

    public function rtmStart($batch_presence_aware = false, $include_locale = false, $mpim_aware = false, $no_latest = false, $no_unreads = false, $presence_sub = true, $simple_latest = true, $presence_sub = true)
    {
        return self::sendGetRequest("rtm.start", array("token" => $this->token, "batch_presence_aware" => $batch_presence_aware, "presence_sub" => $presence_sub, "include_locale" => $include_locale, "mpim_aware" => $mpim_aware, "no_latest" => $no_latest, "no_unreads" => $no_unreads, "simple_latest" => $simple_latest,));
    }

    /*search*/
    public function searchAll($query, $count = 20, $page = 1, $sort = "score", $sort_dir = "desc", $highlight = false)
    {
        return self::sendGetRequest("search.all", array("token" => $this->token, "query" => $query, "count" => $count, "page" => $page, "sort" => $sort, "sort_dir" => $sort_dir, "highlight" => $highlight));
    }

    public function searchFiles($query, $count = 20, $page = 1, $sort = "score", $sort_dir = "desc", $highlight = false)
    {
        return self::sendGetRequest("search.files", array("token" => $this->token, "query" => $query, "count" => $count, "page" => $page, "sort" => $sort, "sort_dir" => $sort_dir, "highlight" => $highlight));
    }

    public function searchMessages($query, $count = 20, $page = 1, $sort = "score", $sort_dir = "desc", $highlight = false)
    {
        return self::sendGetRequest("search.messages", array("token" => $this->token, "query" => $query, "count" => $count, "page" => $page, "sort" => $sort, "sort_dir" => $sort_dir, "highlight" => $highlight));
    }

    /*stars*/
    public function starsAdd($channel = "", $timestamp = 0, $file = "", $file_comment = "")
    {
        return self::sendPostRequest("stars.add", array("token" => $this->token, "channel" => $channel, "timestamp" => $timestamp, "file" => $file, "file_comment" => $file_comment));
    }

    public function starsList($cursor = "", $count = 100, $limit = 0, $page = 1)
    {
        return self::sendGetRequest("stars.list", array("token" => $this->token, "cursor" => $cursor, "count" => $count, "limit" => $limit, "page" => $page));
    }

    public function starsRemove($channel = "", $timestamp = 0, $file = "", $file_comment = "")
    {
        return self::sendPostRequest("stars.remove", array("token" => $this->token, "channel" => $channel, "timestamp" => $timestamp, "file" => $file, "file_comment" => $file_comment));
    }

    /*team*/
    public function teamAccessLogs($before = 0, $page = 1, $count = 100)
    {
        $before = $before = 0 ? "now" : $before;
        return self::sendGetRequest("team.accessLogs", array("token" => $this->token, "before" => $before, "page" => $page, "count" => $count));
    }

    public function teamBillableInfo($user)
    {
        return self::sendGetRequest("team.billableInfo", array("token" => $this->token, "user" => $user));
    }

    public function teamInfo($team = "")
    {
        return self::sendGetRequest("team.info", array("token" => $this->token, "team" => $team));
    }

    public function teamIntegrationLogs($user = "", $app_id = "", $change_type = "added", $count = 100, $page = 1, $service_id = "")
    {
        return self::sendGetRequest("team.integrationLogs", array("token" => $this->token, "user" => $user, "app_id" => $app_id, "change_type" => $change_type, "count" => $count, "page" => $page, "service_id" => $service_id));
    }

    public function teamProfileGet($visibility = "all")
    {
        return self::sendGetRequest("team.profile.get", array("token" => $this->token, "visibility" => $visibility));
    }

    /*usergroups*/
    public function usergroupsCreate($name, $channels = "", $description = "", $handle = "", $include_count = false)
    {
        return self::sendPostRequest("usergroups.create", array("token" => $this->token, "name" => $name, "channels" => $channels, "description" => $description, "handle" => $handle, "include_count" => $include_count));
    }

    public function usergroupsDisable($usergroup, $include_count = false)
    {
        return self::sendPostRequest("usergroups.disable", array("token" => $this->token, "usergroup" => $usergroup, "include_count" => $include_count));
    }

    public function usergroupsEnable($usergroup, $include_count = false)
    {
        return self::sendPostRequest("usergroups.enable", array("token" => $this->token, "usergroup" => $usergroup, "include_count" => $include_count));
    }

    public function usergroupsList($include_count = false, $include_disabled = false, $include_users = false)
    {
        return self::sendGetRequest("usergroups.list", array("token" => $this->token, "include_count" => $include_count, "include_disabled" => $include_disabled, "include_users" => $include_users));
    }

    public function usergroupsUpdate($usergroup, $channels = "", $description = "", $name = "", $handle = "", $include_count = false)
    {
        return self::sendPostRequest("channels.create", array("token" => $this->token, "usergroup" => $usergroup, "channels" => $channels, "description" => $description, "name" => $name, "handle" => $handle, "include_count" => $include_count));
    }

    /*usergroups.users*/
    public function usergroupsUsersList($usergroup, $include_disabled = false)
    {
        return self::sendGetRequest("usergroups.users.list", array("token" => $this->token, "usergroup" => $usergroup, "include_disabled" => $include_disabled));
    }

    public function usergroupsUsersUpdate($usergroup, $users, $include_count = false)
    {
        $users = is_array($users) ? implode(",", $users) : $users;
        return self::sendPostRequest("usergroups.users.update", array("token" => $this->token, "usergroup" => $usergroup, "users" => $users, "include_count" => $include_count));
    }

    /*users*/
    public function usersConversations($user = "", $cursor = "", $exclude_archived = false, $limit = 100, $types = "public_channel")
    {
        return self::sendGetRequest("users.conversations", array("token" => $this->token, "user" => $user, "cursor" => $cursor, "exclude_archived" => $exclude_archived, "limit" => $limit, "types" => $types));
    }

    public function usersDeletePhoto()
    {
        return self::sendGetRequest("users.deletePhoto", array("token" => $this->token));
    }

    public function usersGetPresence($user)
    {
        return self::sendGetRequest("users.getPresence", array("token" => $this->token, "user" => $user));
    }

    public function usersIdentity()
    {
        return self::sendGetRequest("users.identity", array("token" => $this->token));
    }

    public function usersInfo($user, $include_locale = false)
    {
        return self::sendGetRequest("users.info", array("token" => $this->token, "user" => $user, "include_locale" => $include_locale));
    }

    public function usersList($cursor = "", $limit = 0, $include_locale = false)
    {
        return self::sendGetRequest("users.list", array("token" => $this->token, "cursor" => $cursor, "limit" => $limit, "include_locale" => $include_locale));
    }

    public function usersLookupByEmail($email)
    {
        return self::sendGetRequest("users.lookupByEmail", array("token" => $this->token, "email" => $email));
    }

    public function usersSetActive()
    {
        return self::sendPostRequest("users.setActive", array("token" => $this->token));
    }

    public function usersSetPhoto($image, $crop_w = 0, $crop_x = 0, $crop_y = 0)
    {
        //TODO добавить multipart отправку фото на сервер slack
        return false;
    }

    public function usersSetPresence($presence)
    {
        return self::sendPostRequest("users.setPresence", array("token" => $this->token, "presence" => $presence));
    }

    public function usersProfileGet($user, $include_labels = false)
    {
        return self::sendGetRequest("users.profile.get", array("token" => $this->token, "user" => $user, "include_labels" => $include_labels));
    }

    /**
     * @param string $name
     * @param array $profile
     * @param string $user
     * @param string $value
     * @return array
     */
    public function usersProfileSet($name = "", $profile = array(), $user = "", $value = "")
    {

        return self::sendPostRequest("users.profile.set", array("token" => $this->token, "name" => $name, "profile" => $profile, "user" => $user, "value" => $value));
    }

    /*views*/
    public function viewsOpen($trigger_id, $view)
    {
        return self::sendPostRequest("views.open", array("token" => $this->token, "trigger_id" => $trigger_id, "view" => $view));
    }

    public function viewsPush($trigger_id, $view)
    {
        return self::sendPostRequest("views.push", array("token" => $this->token, "trigger_id" => $trigger_id, "view" => $view));
    }

    public function viewsUpdate($view, $view_id = "", $external_id = "", $hash = "")
    {
        return self::sendPostRequest("views.update", array("token" => $this->token, "view" => $view, "view_id" => $view_id, "external_id" => $external_id, "hash" => $hash));
    }
}