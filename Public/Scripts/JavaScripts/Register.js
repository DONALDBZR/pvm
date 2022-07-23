class Application extends React.Component {
    render() {
        return [<Header />, <Main />, <Footer />];
    }
}
class Header extends Application {
    render() {
        return (
            <header id="registerHeader">
                <div id="home">
                    <a href="../">
                        <img src="../Public/Images/(1569).png" />
                    </a>
                </div>
                <div id="pageDescription">
                    Password Manager Registration Page
                </div>
            </header>
        );
    }
}
class Main extends Application {
    constructor(props) {
        super(props);
        this.state = {
            mailAddress: "",
            success: "",
            message: "",
            url: "",
        };
    }
    handleChange(event) {
        const target = event.target;
        const value = target.value;
        const name = target.name;
        this.setState({
            [name]: value,
        });
    }
    handleSubmit(event) {
        const delay = 4000;
        event.preventDefault();
        fetch("./UserRegister.php", {
            method: "POST",
            body: JSON.stringify({
                mailAddress: this.state.mailAddress,
            }),
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    success: data.success,
                    message: data.message,
                    url: data.url,
                })
            )
            .then(() => this.redirector(delay));
    }
    redirector(delay) {
        setTimeout(() => {
            window.location.url = this.state.url;
        }, delay);
    }
    render() {
        return (
            <main id="registerMain">
                <form method="POST" onSubmit={this.handleSubmit.bind(this)}>
                    <div id="mailAddress">
                        <div>Mail Address:</div>
                        <input
                            type="email"
                            name="mailAddress"
                            placeholder="Mail Address"
                            value={this.state.mailAddress}
                            onChange={this.handleChange.bind(this)}
                            required
                        />
                    </div>
                    <div id="button">
                        <button>Register</button>
                    </div>
                    <div id="serverRendering">
                        <h1 id={this.state.success}>{this.state.message}</h1>
                    </div>
                </form>
            </main>
        );
    }
}
class Footer extends Application {
    render() {
        return (
            <footer id="registerFooter">
                Already have an account for that user? Prompt him/her to{" "}
                <a href="../Login">log in</a>
            </footer>
        );
    }
}
ReactDOM.render(<Application />, document.getElementById("app"));
