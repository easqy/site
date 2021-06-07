import {
	Component,
	render
} from '@wordpress/element';
import './main.scss';

import $ from "jquery";



class Latest_results extends Component {     // instead of: ...extends React.Component
	constructor(props) {
		super(props);
		this.state = { loading: true, results: [] };
	};

	componentDidMount() {
		const me = this;
		$.ajax({
			url: easqy.ajaxurl,
			method: "POST",
			data: {
				action: "easqy_sc_latest_results",
				security: easqy.security
			},
			success: function (data) {
				if (data.success)
					me.setState({ loading: false, results: data.data });
				else
					me.setState({ loading: false, results: [] });
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ loading: false });
			},
		});
	}

	render() {

		const Lines = this.state.results && this.state.results.map((i) => {
			const rank = () => {
				if (i.place === '-') {
					return <p><strong>{i.perf}</strong></p>
				}
				else {
					const rank = (parseInt(i.place) > 0 && parseInt(i.place) < 4) ? 'easqy-result-podium' : '';
					const className = `easqy-result-rank ${rank}`;

					return (
						<p className={className}>{i.place}<sup>{i.place === '1' ? 'ier(e)' : 'ième'}</sup></p>
					);
				}
			}

			return (
				<div className={"easqy-latest-result"}>
					<div>{i.date}&nbsp;-&nbsp;{i.town}&nbsp;-&nbsp;{i.epreuve}&nbsp;-&nbsp;{i.tour}</div>
					<div style={{ display: "flex", 'flex-direction': 'row', 'justify-content': 'flex-start' }}>
						<div>{rank()}</div>
						<div>
							{
								((i.athlete !== null) && (<>&nbsp;:&nbsp;<strong>{i.athlete.name}</strong><br /></>)) ||
								((i.athlete === null) && (<>&nbsp;</>))
							}
						</div>
						<div>
							&nbsp;:&nbsp;<strong>{i.perf}</strong>
						</div>
					</div>
				</div>
			);
		}
		);

		return (
			<div className={'easqy-latest-results'}>
				{this.state.loading && <h2>Chargement...en cours </h2>}
				{Lines}
				<div style={{ "text-align": 'right' }}><a
					target="_blank"
					href={
						"https://bases.athle.fr/asp.net/liste.aspx?frmpostback=true&frmbase=resultats&frmmode=1&frmespace=0&frmsaison=" +
						new Date().getFullYear() + "&frmclub=078140&frmnom=&frmprenom=&frmsexe=&frmlicence=&frmdepartement=&frmligue=&frmcomprch="}>
					Les résultats de l'année du club ...</a></div>
			</div>
		)
	}

}

// Render the app inside our shortcode's #app div
(function () {

	const elts = document.getElementsByClassName('easqy-shortcode-latest-results');
	for (let elt of elts)
		render(<Latest_results />, elt);

})();

