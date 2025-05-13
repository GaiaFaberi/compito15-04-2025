import {useState} from 'react';
export default function ScuoleTable(props){
    const s = props.scuola;
    const [conferma, setConferma] = useState(false);
    const [isCancellato, setisCancellato] = useState(false);

    async function deleteScuola(){
      const data = await fetch("http://localhost:8080/scuole/" + s.id, {method:"DELETE"});
      setisCancellato(true);
    }

    return(
      <>
      {isCancellato ? (
        <>
        </>
      ) : (
        <tr>
        <td>{s.id}</td>
        <td>{s.nome}</td>
        <td>{s.indirizzo}</td>
        <td>
          {conferma ? (
            <>
            <div> sei sicuro?</div>
            <button onClick={deleteScuola}>si</button>
            <button onClick={()=> setConferma(false)}>no</button>
            </>
            
          ) : (
            <button onClick={()=> setConferma(true)}>Elimina</button>
          )}
        </td>
      </tr>
      )}
      </>
      )
    
}