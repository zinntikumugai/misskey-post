<?php
class MisskeySettings {
    public const OptionName = Misskey::Slag.'_options';
    private $options;
    protected $defaults = [
        'url' => 'https://misskey.xyz',
        'appSecret' => '',
        'i' => ''
    ];

    public function __construct() {
        $this->options = get_option(MisskeySettings::OptionName, $this->defaults);

        add_action('admin_menu', [$this, 'addMenu']);
    }

    public function addMenu() {
        add_options_page(
            Misskey::Name,
            Misskey::Name,
            'administrator',
            Misskey::Slag,
            [$this, 'load_plugin_admin_page']
        );
    }

    private function optionUpdates() {
        $url = filter_input(INPUT_POST, 'url');
        $appSecret = filter_input(INPUT_POST, 'appSecret');
        if ($url !== false && $url !== null)
            $this->options['url'] = $url;
        if ($appSecret !== false && $appSecret !== null)
            $this->options['appSecret'] = $appSecret;
        update_option(MisskeySettings::OptionName, $this->options);
    }
    
    private function setI($i) {
        $this->options['i'] = $i;
        update_option(MisskeySettings::OptionName, $this->options);
    }

    private function Auth(&$url) {
        $auth = filter_input(INPUT_POST, 'auth');
        $authIdx = filter_input(INPUT_POST, 'authIdx');
        require_once(__DIR__ . '/lib/Auth.php');
        $ma = new MisskeyAuth($this->options['url'], $this->options['appSecret']);
        if(!isset($authIdx))
            $authIdx = null;
        if ($auth !== false && $auth !== null) {
            switch($authIdx) {
                case 0:
                    $url = $ma->auth();
                    $ma->saveSession();
                    break;
                
                case 1:
                    $ma->loadSession();
                    $ma->dropSession();
                    $at = $ma->getAccessToken();
                    $this->setI($ma->getI());
                    break;

                case 2:
                    break;
                default:
                    $authIdx = -1;
            }
            $authIdx += 0;
            ++$authIdx;
        }else {
            $authIdx = -1;
        }
        return $authIdx;
    }

    public function load_plugin_admin_page() {
        $authURL = "";
        $this->optionUpdates();
        $authIdx = $this->Auth($authURL);
        ?>
<h1><?= Misskey::Name ?></h1>
<h2>認証設定</h2>
<form method="post">
    <table>
        <tr>
            <th>URL<br>(URLの末尾に<code>/</code>を含めないでください)</th>
            <td><input type="text" name="url" value="<?=esc_attr($this->options['url'])?>"></td>
        </tr>
        <tr>
            <th>appSecret <a href="<?= esc_attr($this->options['url']) ?>/dev" target="_blank">アプリを作る</a><br><code>アカウントの情報を見る</code>,<code>投稿する</code>を権限で付けてください</th>
            <td><input type="text" name="appSecret" value="<?= esc_attr($this->options['appSecret']) ?>"></td>
        </tr>
    </table>
    <?php if ($authIdx === 1) : ?>
        <p><a href="<?=$authURL?>" target="_blank">こちら</a>を開き、認証してください。</p>
    <?php elseif($authIdx === 2): ?>
        <p>認証が完了しました(多分</p>
    <?php elseif(isset($this->options['i'])):?>
        <p>すでに認証しています。</p>
    <?php endif; ?>
    <p>クソコードなのでたまにスカるときがあります。(正常です)何度か挑戦してみてください</p>
    <input type="hidden" name="authIdx" value="<?=$authIdx?>">
    <?php
        if(!isset($authIdx) || !isset($authURL) || $authURL === "")
            submit_button('保存して認証を開始する', 'primary', 'auth');
        else
            submit_button('認証を続ける', 'primary', 'auth');
        submit_button('保存');
    ?>
</form>
<?php
    }
}