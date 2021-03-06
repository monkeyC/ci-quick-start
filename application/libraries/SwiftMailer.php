<?php

class SwiftMailer {

    /**
     * @var string $host The hostname or ip address of the host to send the message from
     */
    protected $host;

    /**
     * @var string $username The username to use for smtp authorisation
     */
    protected $username;

    /**
     * @var string $password The password to use for smtp authorisation
     */
    protected $password;

    /**
     * @var string $from_address The address to send the message from
     */
    protected $from_address;

    /**
     * @var string $from_name The name to send the message from
     */
    protected $from_name;

    /**
     * @var string $encryption The type of encryption to use
     */
    protected $encryption;

    /**
     * @var int $port The port to connect the smtp server on
     */
    protected $port;

    /**
     * @var int $local_port The port to connect to the local smtp server on
     */
    protected $local_port;

    /**
     * @var string $return_path The address to specify as the return path for bounces
     */
    protected $return_path;

    /**
     * @var array $to The addresses to send the message to
     */
    protected $to;

    /**
     * @var string $subject The subject to put on the message
     */
    protected $subject;

    /**
     * @var string $content The html content to include in the message
     */
    protected $content;

    /**
     * @var array $attachments Any attachments to include in the message
     */
    protected $attachments;

    /**
     * @var array $cc The addresses to cc on the message
     */
    protected $cc;

    /**
     * @var array $bcc The addresses to bcc on the message
     */
    protected $bcc;

    /**
     * @var array $reply_to The addresses to use as the reply to for the message
     */
    protected $reply_to;

    public function __construct($options = null) {
        $ci = & get_instance();
        
        $ci->config->load_yml('app/swiftmailer');
        $options = $ci->config->item('swiftmailer');

        $this->host = $options["host"];
        $this->username = $options["username"];
        $this->password = $options["password"];

        $this->from_address = $options["from_address"];
        $this->from_name = $options["from_name"];

        $this->encryption = $options["encryption"];
        $this->port = $options["port"];
        $this->local_port = $options["local_port"];

        $this->return_path = $options["return_path"];

        $this->to = [];
        $this->subject = "";
        $this->content = "";

        $this->attachments = [];

        $this->cc = [];
        $this->bcc = [];
        $this->reply_to = [];
    }

    /**
     * Set the subject of the message, discarding any previously set subject.
     *
     * @param string $subject The subject to use
     *
     * @return Mailer
     */
    public function setSubject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the recipient of the message, discarding any previously defined recipients.
     *
     * @param string|array $address An address, either as a string of just the email address, or an array where the key is the address and the value is the recipient's name
     *
     * @return Mailer
     */
    public function setRecipient($address) {
        $this->to = [];

        $this->addRecipient($address);

        return $this;
    }

    /**
     * Add a recipient to the message.
     *
     * @param string|array $address An address, either as a string of just the email address, or an array where the key is the address and the value is the recipient's name
     *
     * @return Mailer
     */
    public function addRecipient($address) {
        if (!is_array($address)) {
            $address = [$address => $address];
        }

        $this->to = array_merge($this->to, $address);

        return $this;
    }

    /**
     * Set the cc for the message, discarding any previously defined cc addresses.
     *
     * @param string|array $address An address, either as a string of just the email address, or an array where the key is the address and the value is the recipient's name
     *
     * @return Mailer
     */
    public function setCc($address) {
        $this->cc = [];

        $this->addCc($address);

        return $this;
    }

    /**
     * Add a cc to the message.
     *
     * @param string|array $address An address, either as a string of just the email address, or an array where the key is the address and the value is the recipient's name
     *
     * @return Mailer
     */
    public function addCc($address) {
        if (!is_array($address)) {
            $address = [$address => $address];
        }

        $this->cc = array_merge($this->cc, $address);

        return $this;
    }

    /**
     * Set the bcc for the message, discarding any previously defined bcc addresses.
     *
     * @param string|array $address An address, either as a string of just the email address, or an array where the key is the address and the value is the recipient's name
     *
     * @return Mailer
     */
    public function setBcc($address) {
        $this->bcc = [];

        $this->addBcc($address);

        return $this;
    }

    /**
     * Add a bcc to the message.
     *
     * @param string|array $address An address, either as a string of just the email address, or an array where the key is the address and the value is the recipient's name
     *
     * @return Mailer
     */
    public function addBcc($address) {
        if (!is_array($address)) {
            $address = [$address => $address];
        }

        $this->bcc = array_merge($this->bcc, $address);

        return $this;
    }

    /**
     * Set the reply to address for the message, discarding any previously defined reply to addresses.
     *
     * @param string|array $address An address, either as a string of just the email address, or an array where the key is the address and the value is the recipient's name
     *
     * @return Mailer
     */
    public function setReplyTo($address) {
        $this->reply_to = [];

        $this->addReplyTo($address);

        return $this;
    }

    /**
     * Add a reply to address to the message.
     *
     * @param string|array $address An address, either as a string of just the email address, or an array where the key is the address and the value is the recipient's name
     *
     * @return Mailer
     */
    public function addReplyTo($address) {
        if (!is_array($address)) {
            $address = [$address => $address];
        }

        $this->reply_to = array_merge($this->reply_to, $address);

        return $this;
    }

    /**
     * Set the content of the body of the message, discarding any previously added content.
     *
     * @param string $content The html content to add
     *
     * @return Mailer
     */
    public function setContent($content) {
        $this->content = "";
        return $this->addContent($content);
    }

    /**
     * Add content to the body of the message.
     *
     * @param string $content The html content to add
     *
     * @return Mailer
     */
    public function addContent($content) {
        $this->content .= $content;

        return $this;
    }

    /**
     * Set the content of the body of the message, discarding any previously added content.
     *
     * @param string $view The name of the view to use
     * @param array $params Parameters to pass to the view
     *
     * @return Mailer
     */
    public function setView($view, array $params = null) {
        $this->content = "";
        return $this->addView($view, $params);
    }

    /**
     * Add content to the body of the message.
     *
     * @param string $view The name of the view to use
     * @param array $params Parameters to pass to the view
     *
     * @return Mailer
     */
    public function addView($view, array $params = null) {
        if (!is_array($params)) {
            $params = [];
        }
        $content = Blade::make($view, $params);
        return $this->addContent($content);
    }

    /**
     * Add an attachment to the message.
     *
     * @param string $path The full path to the file to attach
     * @param string $filename An optional filename to use (instead of the filename from the path)
     *
     * @return Mailer
     */
    public function addAttachment($path, $filename = null) {
        $this->attachments[$path] = $filename;

        return $this;
    }

    /**
     * Send the message.
     *
     * @param string|array $address An additional to address for the message, either as a string of just the email address, or an array where the key is the address and the value is the recipient's name
     *
     * @return int (number of successful recipients)
     */
    public function send($address = null) {
        if ($address) {
            $this->addRecipient($address);
        }
        if (count($this->to) < 1) {
            throw new \Exception("No recipients specified to send the email to");
        }
        $keys = array_keys($this->to);
        if (!$keys[0]) {
            throw new \Exception("Invalid recipient specified to send the email to");
        }

        # Connect to the smtp server
        if ($this->host) {
            $smtp = Swift_SmtpTransport::newInstance($this->host, $this->port, $this->encryption);
        } else {
            $smtp = Swift_SmtpTransport::newInstance("localhost", $this->local_port);
        }
        if ($this->username) {
            $smtp->setUsername($this->username);
        }
        if ($this->password) {
            $smtp->setPassword($this->password);
        }

        # Create a new instance of the swift mailer
        $swift = Swift_Mailer::newInstance($smtp);

        # Create a new message
        $mail = Swift_Message::newInstance();

        # Set the bounce return path if one has been specified
        if ($this->return_path) {
            $mail->setReturnPath($this->return_path);
        }

        $html = "<html>";
        $html .= "<head>";
        $html .= "<style type='text/css'>";
        $html .= "body { margin:0px; font-family:arial,helvetica,sans-serif; font-size:13px; }";
        $html .= "</style>";
        $html .= "</head>";
        $html .= "<body>";
        $html .= $this->content;
        $html .= "</body>";
        $html .= "</html>";

        # Give the message a subject
        $mail->setSubject($this->subject);

        # Set the from address
        $mail->setFrom([$this->from_address => $this->from_name]);

        # Set the to address
        $mail->setTo($this->to);

        # Add the html body and an alternative plain text version
        $mail->setBody($html, "text/html");
        $mail->addPart("To view this message, please use an HTML compatible email viewer.", "text/plain");

        # If attachments have been specified then attach them
        foreach ($this->attachments as $path => $filename) {
            $attachment = Swift_Attachment::fromPath($path);
            if ($filename) {
                $attachment->setFilename($filename);
            }
            $mail->attach($attachment);
        }

        # Set the relevant cc
        if (count($this->cc) > 0) {
            $mail->setCc($this->cc);
        }

        # Set the relevant bcc
        if (count($this->bcc) > 0) {
            $mail->setBcc($this->bcc);
        }

        # Set the relevant reply-to
        if (count($this->reply_to) > 0) {
            $mail->setReplyTo($this->reply_to);
        }

        # Send the message
        $result = $swift->send($mail);

        return $result;
    }

}
