import logo from './logo.svg';
import './App.css';
import {useState} from 'react';
import ScuoleTable from './ScuoleTable.js'

function App() {

  const [loading, setLoading] = useState(false);
  const [scuole, setScuole] = useState([]);
  async function caricaScuole(){
    setLoading(true);
    const data = await fetch("http://localhost:8080/scuole", {method:"GET"});
    const mieiDati = await data.json();
    setScuole(mieiDati);
  }

  return(
    <div className="App">
      {scuole.length > 0 ? (
      <ScuoleTable myArray ={scuole}></ScuoleTable>
      ) : (
        <div>
        {loading ? (
          <div>caricamento in corso</div>
        ) : (
          <button onClick={caricaScuole}>Carica scuole</button>
        )
        }
        </div>
      )}
    </div>
  );
}

export default App;
