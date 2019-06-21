'use strict';
// this is React example
class LikeButton extends React.Component {
    constructor(props) {
        super(props);
        this.state = { liked: false };
    }

    render() {
        if (this.state.liked) {
            return 'You liked this.';
        }

        return e(
            'button',
            { onClick: () => this.setState({ liked: true }) },
            'Like'
        );
    }
}



class UploadSuccessButton extends React.Component {
    render() {
        return React.createElement(
            'div',
            {
                className: 'alert alert-success',
                'role': 'alert',
            },
            React.createElement('p', {className: 'col-lg-8'}, "Great job, your csv it's ready for implementing!"),
            React.createElement('div',
                {
                    className: 'btn blue-dark',
                    'data-url': 'app_dev.php/admin/validation',
                    'id': 'btnupload'
                },
                React.createElement('span', {}, 'Validation')
            )
        );
    }
}

//
// const divAlert = React.createElement(
//     'div',
//     {className: 'alert'},
// );
//
// let asdf = React.createElement(
//     'div',
//     {
//         children : React.createElement('p', {className : 'Great!'}),
//         className: 'alert alert-success',
//         'role' : 'alert'},
//
// );
//
//
//
// let div = $("<div>").addClass("alert alert-"+type).attr("role", "alert");
//
// let success = $("<p>").text("Great job, your csv it's ready for upload!");
// let errorSpan = $("<span>").attr("id", "noOfErrors").text(noOfErrors);
// let error = $("<p>").attr("id", "errorrParag").append("Your csv have ", errorSpan, " errors, please review them and upload again until has no errors!");
// let p = (type === "success") ? success : error;
// return div.append(p);